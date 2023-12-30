<?php

namespace App\Repository;

use App\Entity\CartPosition;
use App\Model\CartPositionDTO;
use Doctrine\Persistence\ObjectRepository;

/**
 * @extends \Doctrine\Persistence\ObjectRepository<\App\Entity\CartPosition>
 */
interface CartPositionRepositoryInterface extends ObjectRepository
{
   /**
    * @param \App\Model\CartPositionDTO $data
    *
    * @return \App\Entity\CartPosition
    *   The entity filled from the provided data.
    */
    public function createFromData(CartPositionDTO $data): CartPosition;

    /**
     * @param \App\Entity\CartPosition $entity
     * @param \App\Model\CartPositionDTO $data
     *
     * @return void
     */
    public function updateWithData(CartPosition &$entity, CartPositionDTO $data): void;
}
