<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartPosition;
use App\Entity\Product;
use App\Model\CartPositionDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartPosition>
 *
 * @method CartPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartPosition[]    findAll()
 * @method CartPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartPositionRepository extends ServiceEntityRepository implements CartPositionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartPosition::class);
    }

    /**
     * @inheritDoc
     */
    public function createFromData(CartPositionDTO $data): CartPosition
    {
        $entity = new CartPosition();

        $this->updateWithData($entity, $data);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function updateWithData(CartPosition &$entity, CartPositionDTO $data): void
    {
        if (isset($data->cart)) {
            $cart = $this->getEntityManager()->getRepository(Cart::class)
                ->find($data->cart);
            $entity->setCart($cart);
        }

        if (isset($data->product)) {
            $product = $this->getEntityManager()->getRepository(Product::class)
                ->find($data->product);
            $entity->setProduct($product);
        }

        if (isset($data->quantity)) {
            $entity->setQuantity($data->quantity);
        }
    }
}
