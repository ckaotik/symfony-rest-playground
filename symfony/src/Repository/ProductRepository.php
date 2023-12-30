<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    /**
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

   /**
    * @param string $name
    *
    * @return Product[]
    *   Returns an array of Product objects
    */
    public function findByName($name): array
    {
        return $this->createQueryBuilder('p')
           ->andWhere('p.name = :name')
           ->setParameter('name', $name)
           ->orderBy('p.id', 'ASC')
           ->getQuery()
           ->getResult()
        ;
    }

   /**
    * @param string $name
    *
    * @return Product|null
    *   A product with the given name.
    */
    public function findOneByName(string $name): ?Product
    {
        return $this->createQueryBuilder('p')
           ->andWhere('p.name = :name')
           ->setParameter('name', $name)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }
}
