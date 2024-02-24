<?php

namespace App\Entity;

use App\Repository\SemaineReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SemaineReservationRepository::class)]
class SemaineReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column]
    private ?int $numeroSemaine = null;

    #[ORM\OneToMany(targetEntity: JourReservation::class, mappedBy: 'semaineReservation')]
    private Collection $jourReservation;

    public function __construct()
    {
        $this->jourReservation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getNumeroSemaine(): ?int
    {
        return $this->numeroSemaine;
    }

    public function setNumeroSemaine(int $numeroSemaine): static
    {
        $this->numeroSemaine = $numeroSemaine;

        return $this;
    }

    /**
     * @return Collection<int, JourReservation>
     */
    public function getJourReservation(): Collection
    {
        return $this->jourReservation;
    }

    public function addJourReservation(JourReservation $jourReservation): static
    {
        if (!$this->jourReservation->contains($jourReservation)) {
            $this->jourReservation->add($jourReservation);
            $jourReservation->setSemaineReservation($this);
        }

        return $this;
    }

    public function removeJourReservation(JourReservation $jourReservation): static
    {
        if ($this->jourReservation->removeElement($jourReservation)) {
            // set the owning side to null (unless already changed)
            if ($jourReservation->getSemaineReservation() === $this) {
                $jourReservation->setSemaineReservation(null);
            }
        }

        return $this;
    }
}
