<?php

namespace App\Entity;

use App\Repository\CartRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = '';

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\CartPosition> $positions
     */
    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartPosition::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $positions;

    #[ORM\Column(
        type: Types::DATETIME_MUTABLE,
        options: [
            "default" => "CURRENT_TIMESTAMP"
        ]
    )]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated = null;

    public function __construct()
    {
        $this->positions = new ArrayCollection();

        $this->setCreated($initialValues['created'] ?? new DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, CartPosition>
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(CartPosition $position): static
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
            $position->setCart($this);
        }

        $this->setUpdated(new DateTime('now'));

        return $this;
    }

    public function removePosition(CartPosition $position): static
    {
        if ($this->positions->removeElement($position)) {
            // set the owning side to null (unless already changed)
            if ($position->getCart() === $this) {
                $position->setCart(null);
            }
        }

        $this->setUpdated(new DateTime('now'));

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

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): static
    {
        $this->updated = $updated;

        return $this;
    }
}
