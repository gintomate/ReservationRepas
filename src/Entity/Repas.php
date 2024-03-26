<?php

namespace App\Entity;

use App\Repository\RepasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RepasRepository::class)]
class Repas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank (
        message : "Tous les champs non férié doivent étre remplies.")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'repas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRepas $typeRepas = null;

    #[ORM\ManyToOne(inversedBy: 'repas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JourReservation $jourReservation = null;

    #[ORM\OneToMany(targetEntity: RepasReserve::class, mappedBy: 'repas')]
    private Collection $repasReserves;

    public function __construct()
    {
        $this->repasReserves = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, RepasReserve>
     */
    public function getRepasReserves(): Collection
    {
        return $this->repasReserves;
    }

    public function addRepasReserf(RepasReserve $repasReserf): static
    {
        if (!$this->repasReserves->contains($repasReserf)) {
            $this->repasReserves->add($repasReserf);
            $repasReserf->setRepas($this);
        }

        return $this;
    }

    public function removeRepasReserf(RepasReserve $repasReserf): static
    {
        if ($this->repasReserves->removeElement($repasReserf)) {
            // set the owning side to null (unless already changed)
            if ($repasReserf->getRepas() === $this) {
                $repasReserf->setRepas(null);
            }
        }

        return $this;
    }
}
