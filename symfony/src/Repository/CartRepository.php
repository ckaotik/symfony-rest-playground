<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartPosition;
use App\Model\CartDto;
use App\Model\CartPositionDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cart>
 *
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository implements CartRepositoryInterface
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * @inheritDoc
     */
    public function createFromData(CartDto $data): Cart
    {
        $entity = new Cart();

        $this->updateWithData($entity, $data);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function updateWithData(Cart &$entity, CartDto $data): void
    {
        if (isset($data->comment)) {
            $entity->setComment($data->comment);
        }

        if (isset($data->positions)) {
            /** @var \App\Repository\CartPositionRepositoryInterface $cartPositionRepository */
            $cartPositionRepository = $this->getEntityManager()->getRepository(CartPosition::class);

            foreach ($entity->getPositions() as $position) {
                $entity->removePosition($position);
            }
            foreach ($data->positions as $positionData) {
                $entity->addPosition($cartPositionRepository->createFromData($positionData));
            }
        }
    }
}
