<?php

namespace App\Repository;

use App\Entity\CartPosition;
use App\Model\CartPositionDto;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends \Doctrine\Persistence\ObjectRepository<\App\Entity\CartPosition>
 */
interface CartPositionRepositoryInterface extends ObjectRepository
{
   /**
    * @param \App\Model\CartPositionDto $data
    *
    * @return \App\Entity\CartPosition
    *   The entity filled from the provided data.
    */
    public function createFromData(CartPositionDto $data): CartPosition;

    /**
     * @param \App\Entity\CartPosition $entity
     * @param \App\Model\CartPositionDto $data
     *
     * @return void
     */
    public function updateWithData(CartPosition &$entity, CartPositionDto $data): void;
}
