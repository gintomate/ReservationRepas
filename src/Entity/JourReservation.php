<?php

namespace App\Entity;

use App\Repository\JourReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JourReservationRepository::class)]
class JourReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateJour = null;

    #[ORM\ManyToOne(inversedBy: 'jourReservation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SemaineReservation $semaineReservation = null;

    #[ORM\OneToMany(targetEntity: Repas::class, mappedBy: 'jourReservation')]
    private Collection $repas;

    public function __construct()
    {
        $this->repas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateJour(): ?\DateTimeInterface
    {
        return $this->dateJour;
    }

    public function setDateJour(\DateTimeInterface $dateJour): static
    {
        $this->dateJour = $dateJour;

        return $this;
    }

    public function getSemaineReservation(): ?SemaineReservation
    {
        return $this->semaineReservation;
    }

    public function setSemaineReservation(?SemaineReservation $semaineReservation): static
    {
        $this->semaineReservation = $semaineReservation;

        return $this;
    }

    /**
     * @return Collection<int, Repas>
     */
    public function getRepas(): Collection
    {
        return $this->repas;
    }

    public function addRepa(Repas $repa): static
    {
        if (!$this->repas->contains($repa)) {
            $this->repas->add($repa);
            $repa->setJourReservation($this);
        }

        return $this;
    }

    public function removeRepa(Repas $repa): static
    {
        if ($this->repas->removeElement($repa)) {
            // set the owning side to null (unless already changed)
            if ($repa->getJourReservation() === $this) {
                $repa->setJourReservation(null);
            }
        }

        return $this;
    }
}
