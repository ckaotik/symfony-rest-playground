<?php

namespace App\Model;

class CartDTO
{
    public ?string $comment;

    /**
     * List of cart positions to create.
     *
     * @var array<\App\Model\CartPositionDTO>|null
     */
    public ?array $positions;
}
