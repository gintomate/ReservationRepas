<?php

namespace App\Entity;

use App\Repository\SemaineReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SemaineReservationRepository::class)]
class SemaineReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation', 'semaine', 'semaineResa'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation', 'semaine', 'semaineResa'])]
    #[Assert\Date]
    #[Assert\Unique]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation', 'semaine', 'semaineResa'])]
    #[Assert\Date]
    #[Assert\Unique]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column]
    #[Groups(['reservation', 'semaine', 'semaineResa'])]
    #[Assert\PositiveOrZero]
    private ?int $numeroSemaine = null;

    #[ORM\OneToMany(targetEntity: JourReservation::class, mappedBy: 'semaineReservation')]
    #[Groups(['reservation', 'semaineResa'])]
    private Collection $jourReservation;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'semaine')]
    private Collection $reservations;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation', 'semaineResa'])]
    private ?\DateTimeInterface $dateLimit = null;

    public function __construct()
    {
        $this->jourReservation = new ArrayCollection();
        $this->reservations = new ArrayCollection();
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

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setSemaine($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getSemaine() === $this) {
                $reservation->setSemaine(null);
            }
        }

        return $this;
    }

    public function getDateLimit(): ?\DateTimeInterface
    {
        return $this->dateLimit;
    }

    public function setDateLimit(\DateTimeInterface $dateLimit): static
    {
        $this->dateLimit = $dateLimit;

        return $this;
    }
}
