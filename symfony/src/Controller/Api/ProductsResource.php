<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $product = new Product($request->request->all());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product);
    }

    /**
     * Get a product by id.
     */
    #[Route('/{id}', name: 'products.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse {
        return $this->json($this->productRepository->find($id));
    }
}
