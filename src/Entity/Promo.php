<?php

namespace App\Entity;

use App\Repository\PromoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromoRepository::class)]
class Promo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;


    #[ORM\ManyToOne(inversedBy: 'promos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Section $Section = null;

    #[ORM\OneToMany(targetEntity: UserInfo::class, mappedBy: 'promo')]
    private Collection $userInfos;

    #[ORM\Column(length: 255)]
    private ?string $nomPromo = null;

    public function __construct()
    {
        $this->userInfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }


    public function getSection(): ?Section
    {
        return $this->Section;
    }

    public function setSection(?Section $Section): static
    {
        $this->Section = $Section;

        return $this;
    }

    /**
     * @return Collection<int, UserInfo>
     */
    public function getUserInfos(): Collection
    {
        return $this->userInfos;
    }

    public function addUserInfo(UserInfo $userInfo): static
    {
        if (!$this->userInfos->contains($userInfo)) {
            $this->userInfos->add($userInfo);
            $userInfo->setPromo($this);
        }

        return $this;
    }

    public function removeUserInfo(UserInfo $userInfo): static
    {
        if ($this->userInfos->removeElement($userInfo)) {
            // set the owning side to null (unless already changed)
            if ($userInfo->getPromo() === $this) {
                $userInfo->setPromo(null);
            }
        }

        return $this;
    }

    public function getNomPromo(): ?string
    {
        return $this->nomPromo;
    }

    public function setNomPromo(string $nomPromo): static
    {
        $this->nomPromo = $nomPromo;

        return $this;
    }
}
