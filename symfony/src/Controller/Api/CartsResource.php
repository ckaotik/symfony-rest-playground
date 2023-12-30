<?php

namespace App\Controller\Api;

use App\Entity\Cart;
use App\Model\CartDTO;
use App\Repository\CartRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/v1/carts')]
class CartsResource extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    /**
     * @var \App\Repository\CartRepositoryInterface $entityRepository
     */
    protected CartRepositoryInterface $entityRepository;

    protected const MAX_RESULTS = 50;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        /** @var \App\Repository\CartRepositoryInterface $entityRepository */
        $entityRepository = $entityManager->getRepository(Cart::class);
        $this->entityRepository = $entityRepository;
    }

    /**
     * Get a list of all carts.
     */
    #[Route('/', name: 'carts.list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $limit = min($request->query->get('limit', INF), static::MAX_RESULTS);
        $offset = intval($request->query->get('offset', null));

        $criteria = [];
        if ($comment = $request->query->get('comment')) {
            $criteria['comment'] = $comment;
        }

        return $this->json(
            $this->entityRepository->findBy($criteria, ['id' => 'ASC'], $limit, $offset)
        );
    }

    /**
     * Create a new cart.
     */
    #[Route('/', name: 'carts.add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $entity = new Cart();

        try {
            $this->updateEntityFromRequest($entity, $request);
        } catch (InvalidArgumentException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (Exception $exception) {
            return $this->json(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($entity, JsonResponse::HTTP_CREATED, [
            'Location' => $this->generateUrl(
                'carts.get',
                ['id' => $entity->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }

    /**
     * Get a cart by id.
     */
    #[Route('/{id}', name: 'carts.get', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function get(int $id): JsonResponse
    {
        $entity = $this->entityRepository->find($id);

        return $this->json($entity, $entity ? JsonResponse::HTTP_OK : JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Delete a cart by id.
     */
    #[Route('/{id}', name: 'carts.delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        /** @var \App\Entity\Cart|null $entity */
        $entity = $this->entityManager->getRepository(Cart::class)->find($id);

        try {
            if ($entity === null) {
                throw new InvalidArgumentException('Invalid cart id.');
            }

            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (InvalidArgumentException $exception) {
            return $this->json(
                ['error' => $exception->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        } catch (Exception $exception) {
            return $this->json(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Entity\Cart $entity
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return void
     */
    protected function updateEntityFromRequest(Cart &$entity, Request $request): void
    {
        $content = $request->getPayload();

        $data = new CartDTO();
        foreach ($content->all() as $property => $value) {
            if (property_exists($data, $property)) {
                $data->{$property} = $value;
            }
        }

        $this->entityRepository->updateWithData($entity, $data);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
