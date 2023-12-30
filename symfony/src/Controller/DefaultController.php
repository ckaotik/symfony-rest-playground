<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {
    protected const CURRENT_API_VERSION = 'v1';

    protected ProductRepositoryInterface $productRepository;

    /**
     * @param \App\Repository\ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }

    #[Route('/', name: 'home')]
    //#[Route('/{path}', requirements: ['path' => '(?!api/).+'])]
    function index(Request $request): Response {
        // \Kint::dump($request->attributes->all());
        // \Kint::dump($request->getPathInfo());

        return $this->render('page/home.html.twig', [
            'hostname' => $request->headers->get('host'),
            'products' => $this->productRepository->findAll(),
        ]);
    }

    /**
     * Redirect unversioned API calls to current version.
     */
    #[Route('/api/{version}', requirements: ['version' => '(?!v\d+/).+'])]
    function currentApi(Request $request, string $version): Response {
        return $this->redirect(
            '/api/' . static::CURRENT_API_VERSION . '/' . $version,
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    /**
     * Display a cart.
     */
    #[Route('/cart', name: 'app_cart_index', methods: ['GET'])]
    #[Route('/cart/{id}', name: 'app_cart_show', requirements: ['cart' => '\d+'])]
    function cart(CartRepository $entityRepository, Cart $cart = null): Response {
        if ($cart === null) {
            // Show the most recent cart.
            $cart = $entityRepository->findOneBy([], ['updated' => 'DESC']);
        }

        return $this->render('page/cart.html.twig', [
            'cart' => $cart,
        ]);
    }
}
