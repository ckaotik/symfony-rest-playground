<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ObjectRepository;

interface ProductRepositoryInterface extends ObjectRepository
{
   /**
    * @param string $name
    *
    * @return array<\App\Entity\Product>
    *   Returns an array of Product objects
    */
   public function findByName($name): array;

   /**
    * @param string $name
    *
    * @return \App\Entity\Product|null
    *   A product with the given name.
    */
   public function findOneByName(string $name): ?Product;
}
