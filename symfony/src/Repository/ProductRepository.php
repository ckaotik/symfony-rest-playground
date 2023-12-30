<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\ProductDTO;
use DateTime;
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
    * @inheritdoc
    */
    public function createFromData(ProductDTO $data): Product
    {
        $entity = new Product();

        $this->updateWithData($entity, $data);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function updateWithData(Product &$entity, ProductDTO $data): void
    {
        if (isset($data->name)) {
            $entity->setName($data->name);
        }
        if (isset($data->description)) {
            $entity->setDescription($data->description);
        }
        if (isset($data->imageUrl)) {
            $entity->setImageUrl($data->imageUrl);
        }
        if (isset($data->price)) {
            $entity->setPrice($data->price);
        }
        if (isset($data->status)) {
            $entity->setStatus($data->status);
        }
        if (isset($data->created)) {
            $entity->setCreated(new DateTime($data->created));
        }
    }
}
