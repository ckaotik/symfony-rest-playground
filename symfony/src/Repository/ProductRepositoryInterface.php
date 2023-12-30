<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\ProductDTO;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends \Doctrine\Persistence\ObjectRepository<\App\Entity\Product>
 */
interface ProductRepositoryInterface extends ObjectRepository
{
   /**
    * @param \App\Model\ProductDTO $data
    *
    * @return \App\Entity\Product
    *   The entity filled from the provided data.
    */
    public function createFromData(ProductDTO $data): Product;

    /**
     * @param \App\Entity\Product $entity
     * @param \App\Model\ProductDTO $data
     *
     * @return void
     */
    public function updateWithData(Product &$entity, ProductDTO $data): void;
}
