<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\CartPosition;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route(
    path: '/api/v1/carts/{cart_id}/positions',
    requirements: ['cart_id' => '\d+']
)]
class CartPositionsResource extends AbstractController {
    protected EntityManagerInterface $entityManager;
    protected ObjectRepository $entityRepository;

    protected const MAX_RESULTS = 50;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(CartPosition::class);
    }

    /**
     * Get all positions in the cart.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    #[Route('/', name: 'cart_positions.list', methods: ['GET'])]
    public function list(Request $request, int $cart_id): JsonResponse {
        $limit = $request->query->get('limit') ?? static::MAX_RESULTS;
        $offset = $request->query->get('offset') ?? 0;

        return $this->json(
            $this->entityRepository->findBy([
                'cart' => $cart_id,
            ], ['id' => 'ASC'], $limit, $offset)
        );
    }

    /**
     * Remove all positions from cart.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    #[Route('/', name: 'cart_positions.clear', methods: ['DELETE'])]
    public function clear(int $cart_id): JsonResponse {
        /** @var \App\Entity\Cart $entity */
        $entity = $this->entityManager->getRepository(Cart::class)->find($cart_id);
        foreach ($entity->getPositions() as $cartPosition) {
            $entity->removePosition($cartPosition);
        }

        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($entity, JsonResponse::HTTP_OK, [
            'Location' => $this->generateUrl(
                'cart.get',
                ['id' => $entity->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    /**
     * Add a position to the cart.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    #[Route('/', name: 'cart_positions.add', methods: ['POST'])]
    public function add(Request $request, int $cart_id): JsonResponse {
        $idProduct = $request->request->get('product');
        $quantity = $request->request->get('quantity');

        try {
            $cart = $this->entityManager->getRepository(Cart::class)->find($cart_id);
            if (!$cart) {
                throw new InvalidArgumentException('Invalid cart id');
            }

            /** \App\Entity\Product $product */
            $product = $idProduct ? $this->entityManager->getRepository(Product::class)->find($idProduct) : null;
            if (!$product) {
                throw new InvalidArgumentException('Invalid product id');
            }

            $entity = new CartPosition();
            $entity
                ->setCart($cart)
                ->setProduct($product)
                ->setQuantity($quantity ?? $entity->getQuantity());

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json($entity, JsonResponse::HTTP_CREATED, [
            'Location' => $this->generateUrl(
                'cart_positions.get',
                ['id' => $entity->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    /**
     * Get a position by id.
     */
    #[Route('/{id}', name: 'cart_positions.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse {
        return $this->json($this->entityRepository->find($id));
    }

    /**
     * Update a position by id.
     */
    #[Route('/{id}', name: 'cart_positions.update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $cart_id, int $id): JsonResponse {
        $idProduct = $request->request->get('product');
        $quantity = $request->request->get('quantity');

        try {
            $cart = $this->entityManager->getRepository(Cart::class)->find($cart_id);
            if (!$cart) {
                throw new InvalidArgumentException('Invalid cart id');
            }

            /** \App\Entity\Product $product */
            $product = $idProduct ? $this->entityManager->getRepository(Product::class)->find($idProduct) : null;
            if (!$product) {
                throw new InvalidArgumentException('Invalid product id');
            }

            /** @var \App\Entity\CartPosition $entity */
            $entity = $this->entityRepository->find($id) ?: new CartPosition();
            $entity
                ->setCart($cart)
                ->setProduct($product)
                ->setQuantity($quantity ?? $entity->getQuantity());

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([]);
    }
}
