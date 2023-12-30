<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Model\CartDto;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends \Doctrine\Persistence\ObjectRepository<\App\Entity\Cart>
 */
interface CartRepositoryInterface extends ObjectRepository
{
   /**
    * @param \App\Model\CartDto $data
    *
    * @return \App\Entity\Cart
    *   The entity filled from the provided data.
    */
    public function createFromData(CartDto $data): Cart;

    /**
     * @param \App\Entity\Cart $entity
     * @param \App\Model\CartDto $data
     *
     * @return void
     */
    public function updateWithData(Cart &$entity, CartDto $data): void;
}
