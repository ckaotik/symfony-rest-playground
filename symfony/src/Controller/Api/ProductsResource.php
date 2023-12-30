<?php

namespace App\Controller\Api;

use App\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/products')]
class ProductsResource extends AbstractController {
    protected ProductRepositoryInterface $productRepository;

    /**
     * @param \App\Repository\ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
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
     * Get a product by id.
     */
    #[Route('/{id}', name: 'products.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse {
        return $this->json($this->productRepository->find($id));
    }
}
