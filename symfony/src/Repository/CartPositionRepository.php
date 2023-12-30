<?php

namespace App\Repository;

use App\Entity\CartPosition;
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
class CartPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartPosition::class);
    }
}
