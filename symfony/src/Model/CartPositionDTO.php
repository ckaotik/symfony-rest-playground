<?php

namespace App\Model;

class CartPositionDTO
{
    /**
     * The id of the cart to reference.
     *
     * May only be empty when set directly on a \App\Entity\Cart entity.
     */
    public ?int $cart;

    /**
     * The id of the product to reference.
     */
    public int $product;

    public ?int $quantity;
}
