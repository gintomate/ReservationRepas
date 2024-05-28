<?php

namespace App\Entity;

use App\Repository\UserInfoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserInfoRepository::class)]
class UserInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['userInfo', 'secureUserInfo'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['userInfo', 'secureUserInfo'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['userInfo', 'secureUserInfo'])]
    private ?string $prenom = null;

    #[ORM\OneToOne(mappedBy: 'userInfo', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userInfos')]
    #[Groups(['userInfo', 'secureUserInfo'])]
    private ?Promo $promo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['userInfo', 'secureUserInfo'])]
    #[Assert\NotNull]
    #[Assert\Date]

    private ?\DateTimeInterface $dateDeNaissance = null;

    #[ORM\Column]
    #[Groups(['userInfo', 'secureUserInfo'])]
    #[Assert\PositiveOrZero]
    private ?float $montantGlobal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setUserInfo(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getUserInfo() !== $this) {
            $user->setUserInfo($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    public function setPromo(?Promo $promo): static
    {
        $this->promo = $promo;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): static
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    public function getMontantGlobal(): ?float
    {
        return $this->montantGlobal;
    }

    public function setMontantGlobal(float $montantGlobal): static
    {
        $this->montantGlobal = $montantGlobal;

        return $this;
    }
}
