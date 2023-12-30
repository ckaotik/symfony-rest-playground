<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/v1/products')]
class ProductsResource extends AbstractController {
    protected EntityManagerInterface $entityManager;
    protected ProductRepositoryInterface $productRepository;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->productRepository = $entityManager->getRepository(Product::class);
    }

    /**
     * Get a list of all products.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    #[Route('/', name: 'products.list', methods: ['GET'])]
    public function list(Request $request): JsonResponse {
        return $this->json($this->productRepository->findAll());
    }

    /**
     * Create a new product.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    #[Route('/', name: 'products.add', methods: ['POST'])]
    public function add(Request $request): JsonResponse {
        $entity = new Product($request->request->all());

        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
     * Get a product by id.
     */
    #[Route('/{id}', name: 'products.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse {
        return $this->json($this->productRepository->find($id));
    }
}
