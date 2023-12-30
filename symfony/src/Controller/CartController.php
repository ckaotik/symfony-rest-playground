<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * Create a new cart.
     */
    #[Route('/cart/add', name: 'app_cart_add')]
    public function add(): Response
    {
        $result = $this->handleApiRequest('/carts/', 'POST', [
            'comment' => 'New cart',
        ]);

        if (is_object($result) && isset($result->error)) {
            $this->addFlash('error', sprintf('Failed to create new cart: %s', $result->error));

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
        $result = $this->handleApiRequest('/carts/' . $id, 'DELETE');

        if (is_object($result) && isset($result->error)) {
            $this->addFlash('error', sprintf('Failed to delete cart %d: %s', $id, $result->error));

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
        $result = $this->handleApiRequest('/carts/' . $id . '/positions/', 'DELETE');

        if (is_object($result) && isset($result->error)) {
            $this->addFlash('error', sprintf('Failed to clear cart %d: %s', $id, $result->error));

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
        $result = $this->handleApiRequest('/carts/' . $cart_id . '/positions/' . $id, 'DELETE');

        if (is_object($result) && isset($result->error)) {
            $this->addFlash('error', sprintf('Failed to remove cart position %d: %s', $id, $result->error));
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
        $result = $this->handleApiRequest('/carts/' . $cart_id . '/positions/', 'POST', [
            'product' => $product_id,
            'quantity' => $request->query->get('quantity'),
        ]);

        if (is_object($result) && isset($result->error)) {
            $this->addFlash('error', sprintf('Failed to add product to cart: %s', $result->error));
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
        $result = $this->handleApiRequest('/carts/' . $cart_id . '/positions/' . $id, 'PUT', [
            'product' => $request->query->get('product'),
            'quantity' => $request->query->get('quantity'),
        ]);

        if (is_object($result) && isset($result->error)) {
            $this->addFlash('error', sprintf('Failed to update cart product: %s', $result->error));
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
        $carts = $this->handleApiRequest('/carts/', 'GET');
        if (is_object($carts) && isset($carts->error)) {
            $carts = [];
        }

        // Show the most recently added cart unless explicitly specified.
        $cart = end($carts);
        if ($id !== null) {
            $cart = $this->handleApiRequest('/carts/' . $id, 'GET');
        }

        return $this->render('page/cart.html.twig', [
            'cart' => $cart,
            'carts' => $carts,
        ]);
    }

    /**
     * @todo Figure out why local Docker setup refuses to connect to our own URLs via http_client.
     */
    protected function handleApiRequest(
        string $endpoint,
        string $method = 'GET',
        array $request = [],
        array $query = []
    ): mixed {
        $url = '/api/v1' . $endpoint;
        if ($query) {
            $url .= '?' . http_build_query($query);
        }
        $subRequest = Request::create($url, $method, $request);

        /** @var Response $response */
        $response = $this->container->get('http_kernel')
            ->handle($subRequest, HttpKernelInterface::SUB_REQUEST);


        return json_decode($response->getContent());
    }
}
