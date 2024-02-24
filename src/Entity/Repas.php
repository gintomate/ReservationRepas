<?php

namespace App\Entity;

use App\Repository\RepasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepasRepository::class)]
class Repas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'repas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRepas $typeRepas = null;

    #[ORM\ManyToOne(inversedBy: 'repas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JourReservation $jourReservation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeRepas(): ?TypeRepas
    {
        return $this->typeRepas;
    }

    public function setTypeRepas(?TypeRepas $typeRepas): static
    {
        $this->typeRepas = $typeRepas;

        return $this;
    }

    public function getJourReservation(): ?JourReservation
    {
        return $this->jourReservation;
    }

    public function setJourReservation(?JourReservation $jourReservation): static
    {
        $this->jourReservation = $jourReservation;

        return $this;
    }
}
