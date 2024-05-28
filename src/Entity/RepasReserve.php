<?php

namespace App\Entity;

use App\Repository\RepasReserveRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RepasReserveRepository::class)]
class RepasReserve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation', 'consultation'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'repasReserves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'repasReserves')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['consultation'])]
    private ?Repas $repas = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getRepas(): ?Repas
    {
        return $this->repas;
    }

    public function setRepas(?Repas $repas): static
    {
        $this->repas = $repas;

        return $this;
    }
}
