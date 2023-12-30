<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    private const STATUS_INACTIVE = false;
    private const STATUS_ACTIVE = true;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column]
    private ?int $price = 0;

    #[ORM\Column(
        type: Types::DATETIME_MUTABLE,
        options: [
            "default" => "CURRENT_TIMESTAMP"
        ]
    )]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column]
    private ?bool $status = self::STATUS_INACTIVE;

    /**
     * Provide defaults during construction.
     */
    public function __construct(array $initialValues = [])
    {
        if (isset($initialValues['name'])) {
            $this->setName((string)$initialValues['name']);
        }
        if (isset($initialValues['description'])) {
            $this->setDescription((string)$initialValues['description']);
        }
        if (isset($initialValues['imageUrl'])) {
            $this->setImageUrl((string)$initialValues['imageUrl']);
        }
        if (isset($initialValues['price'])) {
            $this->setPrice(intval($initialValues['price']));
        }
        $this->created = new DateTime($initialValues['created'] ?? 'now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }
}
