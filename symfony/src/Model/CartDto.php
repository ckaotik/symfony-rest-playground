<?php

namespace App\Model;

class CartDto
{
    public ?string $comment;

    /**
     * List of cart positions to create.
     *
     * @var array<\App\Model\CartPositionDto>|null
     */
    public ?array $positions;
}
