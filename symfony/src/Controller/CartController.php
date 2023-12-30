<?php

namespace App\Controller;

use App\Client\ApiClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    protected ApiClientInterface $apiClient;

    /**
     * @param \App\Client\ApiClientInterface $apiClient
     */
    public function __construct(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Create a new cart.
     */
    #[Route('/cart/add', name: 'app_cart_add')]
    public function add(): Response
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/', 'POST', [
            'comment' => 'New cart',
        ]);

        if ($statusCode !== Response::HTTP_CREATED) {
            $this->addFlash('error', sprintf('Failed to create new cart: %s', $result));

            return $this->redirectToRoute('app_cart_index');
        }

        $this->addFlash('success', sprintf('Created new cart #%d', $result->id));

        return $this->redirectToRoute('app_cart_show', ['id' => $result->id]);
    }

    /**
     * Delete a cart.
     */
    #[Route('/cart/{id}/delete', name: 'app_cart_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $id, 'DELETE');

        if ($statusCode !== Response::HTTP_NO_CONTENT) {
            $this->addFlash('error', sprintf('Failed to delete cart %d: %s', $id, $result));

            return $this->redirectToRoute('app_cart_show', ['id' => $id]);
        }

        $this->addFlash('success', sprintf('Deleted cart #%d', $id));

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Clear cart.
     */
    #[Route('/cart/{id}/clear', name: 'app_cart_clear', requirements: ['id' => '\d+'])]
    public function clear(int $id): Response
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $id . '/positions/', 'DELETE');

        if ($statusCode !== Response::HTTP_NO_CONTENT) {
            $this->addFlash('error', sprintf('Failed to clear cart %d: %s', $id, $result));

            return $this->redirectToRoute('app_cart_show', ['id' => $id]);
        }

        $this->addFlash('success', sprintf('Cleared cart #%d', $id));

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Remove product from cart.
     */
    #[Route(
        path: '/cart/{cart_id}/remove/{id}',
        name: 'app_cart_position_remove',
        requirements: [
            'cart_id' => '\d+',
            'id' => '\d+',
        ],
    )]
    public function removePosition(int $cart_id, int $id): Response
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $cart_id . '/positions/' . $id, 'DELETE');

        if ($statusCode !== Response::HTTP_NO_CONTENT) {
            $this->addFlash('error', sprintf('Failed to remove cart position %d: %s', $id, $result));
        } else {
            $this->addFlash('success', sprintf('Removed cart position %d', $id));
        }

        return $this->redirectToRoute('app_cart_show', ['id' => $cart_id]);
    }

    /**
     * Add product to cart.
     */
    #[Route(
        path: '/cart/{cart_id}/add/{product_id}',
        name: 'app_cart_position_add',
        requirements: [
            'cart_id' => '\d+',
            'product_id' => '\d+',
        ],
    )]
    public function addPosition(Request $request, int $cart_id, int $product_id): Response
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $cart_id . '/positions/', 'POST', [
            'product' => $product_id,
            'quantity' => $request->query->get('quantity'),
        ]);

        if ($statusCode !== Response::HTTP_CREATED) {
            $this->addFlash('error', sprintf('Failed to add product to cart: %s', $result));
        } else {
            $this->addFlash('success', sprintf('Added product to cart.'));
        }

        return $this->redirectToRoute('app_cart_show', ['id' => $cart_id]);
    }

    /**
     * Update product in cart.
     */
    #[Route(
        path: '/cart/{cart_id}/update/{id}',
        name: 'app_cart_position_update',
        requirements: [
            'cart_id' => '\d+',
            'id' => '\d+',
        ],
    )]
    public function updatePosition(Request $request, int $cart_id, int $id): Response
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $cart_id . '/positions/' . $id, 'PUT', [
            'product' => $request->query->get('product'),
            'quantity' => $request->query->get('quantity'),
        ]);

        if ($statusCode !== Response::HTTP_OK) {
            $this->addFlash('error', sprintf('Failed to update cart product: %s', $result));
        } else {
            $this->addFlash('success', sprintf('Updated cart product.'));
        }

        return $this->redirectToRoute('app_cart_show', ['id' => $cart_id]);
    }

    /**
     * Display a cart.
     */
    #[Route('/cart', name: 'app_cart_index')]
    #[Route('/cart/{id}', name: 'app_cart_show', requirements: ['cart' => '\d+'])]
    public function show(int $id = null): Response
    {
        [$statusCode, $carts] = $this->apiClient->handleJsonCall('/carts/', 'GET');
        if ($statusCode !== Response::HTTP_OK) {
            $carts = [];
        }

        if ($id !== null) {
            [, $cart] = $this->apiClient->handleJsonCall('/carts/' . $id, 'GET');
        } else {
            // Show the most recently added cart.
            $cart = end($carts);
        }

        if (!$cart) {
            // Create a new cart.
            [$statusCode, $cart] = $this->apiClient->handleJsonCall('/carts/', 'POST', [
                'comment' => 'Cart',
            ]);
        }

        return $this->render('page/cart.html.twig', [
            'cart' => $cart,
            'carts' => $carts,
        ]);
    }
}
