<?php

namespace App\Entity;

use App\Repository\TypeRepasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeRepasRepository::class)]
class TypeRepas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation', 'semaineResa', 'consultation'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reservation', 'semaineResa', 'consultation'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['reservation', 'semaineResa', 'consultation'])]
    #[Assert\PositiveOrZero]
    #[Assert\NotBlank]
    private ?float $tarifPlein = null;

    #[ORM\Column]
    #[Groups(['reservation', 'semaineResa', 'consultation'])]
    #[Assert\PositiveOrZero]
    #[Assert\NotBlank]
    private ?float $tarifReduit = null;

    #[ORM\OneToMany(targetEntity: Repas::class, mappedBy: 'typeRepas')]
    private Collection $repas;

    public function __construct()
    {
        $this->repas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTarifPlein(): ?float
    {
        return $this->tarifPlein;
    }

    public function setTarifPlein(float $tarifPlein): static
    {
        $this->tarifPlein = $tarifPlein;

        return $this;
    }

    public function getTarifReduit(): ?float
    {
        return $this->tarifReduit;
    }

    public function setTarifReduit(float $tarifReduit): static
    {
        $this->tarifReduit = $tarifReduit;

        return $this;
    }

    /**
     * @return Collection<int, Repas>
     */
    public function getRepas(): Collection
    {
        return $this->repas;
    }

    public function addRepas(Repas $repa): static
    {
        if (!$this->repas->contains($repa)) {
            $this->repas->add($repa);
            $repa->setTypeRepas($this);
        }

        return $this;
    }

    public function removeRepas(Repas $repa): static
    {
        if ($this->repas->removeElement($repa)) {
            // set the owning side to null (unless already changed)
            if ($repa->getTypeRepas() === $this) {
                $repa->setTypeRepas(null);
            }
        }

        return $this;
    }
}
