<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\CartPosition;
use App\Entity\Product;
use App\Model\CartPositionDTO;
use App\Repository\CartPositionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
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
class CartPositionsResource extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    /**
     * @var \App\Repository\CartPositionRepositoryInterface<\App\Entity\CartPosition> $entityRepository
     */
    protected CartPositionRepositoryInterface $entityRepository;

    protected const MAX_RESULTS = 50;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        /** @var \App\Repository\CartPositionRepositoryInterface $entityRepository */
        $entityRepository = $entityManager->getRepository(CartPosition::class);
        $this->entityRepository = $entityRepository;
    }

    /**
     * Get all positions in the cart.
     */
    #[Route('/', name: 'cart_positions.list', methods: ['GET'])]
    public function list(Request $request, int $cart_id): JsonResponse
    {
        $limit = min($request->query->get('limit') ?? INF, static::MAX_RESULTS);
        $offset = intval($request->query->get('offset', 0));

        return $this->json(
            $this->entityRepository->findBy([
                'cart' => $cart_id,
            ], ['id' => 'ASC'], $limit, $offset)
        );
    }

    /**
     * Remove all positions from cart.
     */
    #[Route('/', name: 'cart_positions.clear', methods: ['DELETE'])]
    public function clear(int $cart_id): JsonResponse
    {
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
                JsonResponse::HTTP_BAD_REQUEST
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
     * Remove all positions from cart.
     */
    #[Route('/{id}', name: 'cart_positions.delete', methods: ['DELETE'])]
    public function delete(int $cart_id, int $id): JsonResponse
    {
        try {
            $entity = $this->entityRepository->find($id);
            if ($entity === null) {
                throw new InvalidArgumentException('Invalid cart position id.');
            }

            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Add a position to the cart.
     */
    #[Route('/', name: 'cart_positions.add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $entity = new CartPosition();

        try {
            $this->updateEntityFromRequest($entity, $request);
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json($entity, JsonResponse::HTTP_CREATED, [
            'Location' => $this->generateUrl(
                'cart_positions.get',
                [
                    'cart_id' => $entity->getCart()->getId(),
                    'id' => $entity->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    /**
     * Get a position by id.
     */
    #[Route('/{id}', name: 'cart_positions.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse
    {
        $entity = $this->entityRepository->find($id);

        return $this->json($entity, $entity ? JsonResponse::HTTP_OK : JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Update a position by id.
     */
    #[Route('/{id}', name: 'cart_positions.update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        /** @var \App\Entity\CartPosition $entity */
        $entity = $this->entityRepository->find($id) ?: new CartPosition();
        $isNew = $entity->getId() === null;

        try {
            $this->updateEntityFromRequest($entity, $request);
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json($entity, $isNew ? JsonResponse::HTTP_CREATED : JsonResponse::HTTP_OK, [
            'Location' => $this->generateUrl(
                'cart_positions.get',
                [
                    'cart_id' => $entity->getCart()->getId(),
                    'id' => $entity->getId(),
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    /**
     * @param \App\Entity\CartPosition $entity
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    protected function updateEntityFromRequest(CartPosition &$entity, Request $request): void
    {
        $idCart = $request->attributes->get('cart_id');
        $content = json_decode($request->getContent());

        $data = new CartPositionDTO();
        foreach ($content as $property => $value) {
            if (property_exists($data, $property)) {
                $data->{$property} = $value;
            }
        }
        // Do not allow moving positions to another cart.
        $data->cart = $idCart;

        $this->entityRepository->updateWithData($entity, $data);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
