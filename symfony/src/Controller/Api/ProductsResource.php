<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/v1/products')]
class ProductsResource extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected ProductRepositoryInterface $entityRepository;

    protected const MAX_RESULTS = 50;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        /**
         * @var \App\Repository\ProductRepositoryInterface $entityRepository
         */
        $entityRepository = $entityManager->getRepository(Product::class);
        $this->entityRepository = $entityRepository;
    }

    /**
     * Get a list of all products.
     */
    #[Route('/', name: 'products.list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $limit = min($request->query->get('limit', INF), static::MAX_RESULTS);
        $offset = intval($request->query->get('offset', null));

        return $this->json(
            $this->entityRepository->findBy([], ['id' => 'ASC'], $limit, $offset)
        );
    }

    /**
     * Create a new product.
     */
    #[Route('/', name: 'products.add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $entity = new Product($data);

        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return $this->json($entity, JsonResponse::HTTP_CREATED, [
            'Location' => $this->generateUrl(
                'products.get',
                ['id' => $entity->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    /**
     * Delete a product by id.
     */
    #[Route('/{id}', name: 'products.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        /** @var \App\Entity\Product|null $entity */
        $entity = $this->entityManager->getRepository(Product::class)->find($id);

        try {
            if ($entity === null) {
                throw new InvalidArgumentException('Invalid product id.');
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
     * Get a product by id.
     */
    #[Route('/{id}', name: 'products.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse
    {
        $entity = $this->entityRepository->find($id);

        return $this->json($entity, $entity ? JsonResponse::HTTP_OK : JsonResponse::HTTP_NOT_FOUND);
    }
}
