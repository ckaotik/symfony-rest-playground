<?php

namespace App\Repository;

use App\Entity\Product;
use App\Model\ProductDto;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends \Doctrine\Persistence\ObjectRepository<\App\Entity\Product>
 */
interface ProductRepositoryInterface extends ObjectRepository
{
   /**
    * @param \App\Model\ProductDto $data
    *
    * @return \App\Entity\Product
    *   The entity filled from the provided data.
    */
    public function createFromData(ProductDto $data): Product;

    /**
     * @param \App\Entity\Product $entity
     * @param \App\Model\ProductDto $data
     *
     * @return void
     */
    public function updateWithData(Product &$entity, ProductDto $data): void;
}
