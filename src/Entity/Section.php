<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\OneToMany(targetEntity: Promo::class, mappedBy: 'Section')]
    private Collection $promos;

    #[ORM\Column(length: 255)]
    private ?string $nomSection = null;

    #[ORM\Column(length: 255)]
    private ?string $abreviation = null;

    public function __construct()
    {
        $this->promos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

   

    /**
     * @return Collection<int, Promo>
     */
    public function getPromos(): Collection
    {
        return $this->promos;
    }

    public function addPromo(Promo $promo): static
    {
        if (!$this->promos->contains($promo)) {
            $this->promos->add($promo);
            $promo->setSection($this);
        }

        return $this;
    }

    public function removePromo(Promo $promo): static
    {
        if ($this->promos->removeElement($promo)) {
            // set the owning side to null (unless already changed)
            if ($promo->getSection() === $this) {
                $promo->setSection(null);
            }
        }

        return $this;
    }

    public function getNomSection(): ?string
    {
        return $this->nomSection;
    }

    public function setNomSection(string $nomSection): static
    {
        $this->nomSection = $nomSection;

        return $this;
    }

    public function getAbreviation(): ?string
    {
        return $this->abreviation;
    }

    public function setAbreviation(string $abreviation): static
    {
        $this->abreviation = $abreviation;

        return $this;
    }
}
