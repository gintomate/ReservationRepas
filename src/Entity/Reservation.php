<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $sommeTotal = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SemaineReservation $semaine = null;

    #[ORM\OneToMany(targetEntity: RepasReserve::class, mappedBy: 'reservation')]
    private Collection $repasReserves;

    public function __construct()
    {
        $this->repasReserves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSommeTotal(): ?float
    {
        return $this->sommeTotal;
    }

    public function setSommeTotal(float $sommeTotal): static
    {
        $this->sommeTotal = $sommeTotal;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?User $Utilisateur): static
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }

    public function getSemaine(): ?SemaineReservation
    {
        return $this->semaine;
    }

    public function setSemaine(?SemaineReservation $semaine): static
    {
        $this->semaine = $semaine;

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
            $repasReserf->setReservation($this);
        }

        return $this;
    }

    public function removeRepasReserf(RepasReserve $repasReserf): static
    {
        if ($this->repasReserves->removeElement($repasReserf)) {
            // set the owning side to null (unless already changed)
            if ($repasReserf->getReservation() === $this) {
                $repasReserf->setReservation(null);
            }
        }

        return $this;
    }
}
