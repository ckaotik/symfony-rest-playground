<?php

namespace App\Model;

class ProductDto
{
    public ?string $name;

    public ?string $description;

    public ?string $imageUrl;

    public ?int $price;

    public ?bool $status;

    /**
     * A date time string in ISO format, e.g. '2000-01-01T12:00:00+00:00'.
     */
    public ?string $created;
}
