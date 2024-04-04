<?php

namespace App\Entity;

use App\Repository\PromoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PromoRepository::class)]
class Promo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['section', 'userInfo'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['section', 'userInfo'])]
    #[Assert\Date]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['section', 'userInfo'])]
    #[Assert\Date]
    private ?\DateTimeInterface $dateFin = null;


    #[ORM\ManyToOne(inversedBy: 'promos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userInfo'])]
    private ?Section $Section = null;

    #[ORM\OneToMany(targetEntity: UserInfo::class, mappedBy: 'promo')]
    private Collection $userInfos;

    #[ORM\Column(length: 255)]
    #[Groups(['section'])]
    #[Assert\Length(
        min: 3,
        max: 30,
        minMessage: 'Le nom doit faire au moins {{ limit }} charactères.',
        maxMessage: 'Le nom ne doit pas faire plus de {{ limit }} charactères.',
    )]
    #[Assert\Unique]

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
