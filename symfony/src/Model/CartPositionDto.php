<?php

namespace App\Model;

class CartPositionDto
{
    /**
     * The id of the cart to reference.
     */
    public ?int $cart;

    /**
     * The id of the product to reference.
     */
    public ?int $product;

    public ?int $quantity;
}
