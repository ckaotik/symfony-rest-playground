<?php

namespace App\Controller\Api;

use App\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class ProductsController extends AbstractController {
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
    #[Route('/products', name: 'products.get', methods: ['GET'])]
    public function get(Request $request): JsonResponse {
        return $this->json($this->productRepository->findAll());
    }
}
