<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation'])]
    private ?SemaineReservation $semaine = null;

    #[ORM\OneToMany(targetEntity: RepasReserve::class, mappedBy: 'reservation')]
    #[Groups(['reservation'])]
    private Collection $repasReserves;


    #[ORM\Column]
    #[Groups(['reservation'])]
    #[Assert\PositiveOrZero]
    private ?float $montantTotal = null;

    public function __construct()
    {
        $this->repasReserves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMontantTotal(): ?float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): static
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }
}
