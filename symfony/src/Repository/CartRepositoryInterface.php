<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Model\CartDTO;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends \Doctrine\Persistence\ObjectRepository<\App\Entity\Cart>
 */
interface CartRepositoryInterface extends ObjectRepository
{
   /**
    * @param \App\Model\CartDTO $data
    *
    * @return \App\Entity\Cart
    *   The entity filled from the provided data.
    */
    public function createFromData(CartDTO $data): Cart;

    /**
     * @param \App\Entity\Cart $entity
     * @param \App\Model\CartDTO $data
     *
     * @return void
     */
    public function updateWithData(Cart &$entity, CartDTO $data): void;
}
