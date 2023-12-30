<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo Figure out why local Docker setup refuses to connect to our own URLs via http_client.
 */
class CartController extends AbstractController {
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
    #[Route('/cart/{id}/delete', name: 'app_cart_delete')]
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
    #[Route('/cart/{id}/clear', name: 'app_cart_clear')]
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
     * Display a cart.
     */
    #[Route('/cart', name: 'app_cart_index')]
    #[Route('/cart/{id}', name: 'app_cart_show', requirements: ['cart' => '\d+'])]
    public function show(CartRepository $entityRepository, Cart $cart = null): Response {
        if ($cart === null) {
            // Show the most recent cart.
            $cart = $entityRepository->findOneBy([], ['updated' => 'DESC']);
        }

        $response = $this->forward('App\Controller\Api\CartsResource::list');
        $result = json_decode($response->getContent());

        return $this->render('page/cart.html.twig', [
            'cart' => $cart,
            'carts' => $result,
        ]);
    }

    /**
     * @todo Figure out why local Docker setup refuses to connect to our own URLs via http_client.
     */
    protected function handleApiRequest(string $endpoint, string $method = 'GET', array $request = [], array $query = []): mixed
    {
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
