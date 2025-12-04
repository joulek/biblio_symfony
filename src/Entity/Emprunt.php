<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateEmprunt = null;

    #[ORM\Column]
    private ?\DateTime $dateRetourPrevue = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateRetourReelle = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    // 🔥 Relation correcte vers User
    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // 🔥 Relation correcte vers Livre
    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livre $livre = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEmprunt(): ?\DateTime
    {
        return $this->dateEmprunt;
    }

    public function setDateEmprunt(\DateTime $dateEmprunt): static
    {
        $this->dateEmprunt = $dateEmprunt;
        return $this;
    }

    public function getDateRetourPrevue(): ?\DateTime
    {
        return $this->dateRetourPrevue;
    }

    public function setDateRetourPrevue(\DateTime $dateRetourPrevue): static
    {
        $this->dateRetourPrevue = $dateRetourPrevue;
        return $this;
    }

    public function getDateRetourReelle(): ?\DateTime
    {
        return $this->dateRetourReelle;
    }

    public function setDateRetourReelle(?\DateTime $dateRetourReelle): static
    {
        $this->dateRetourReelle = $dateRetourReelle;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    // 🔥 Getter / Setter USER
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // 🔥 Getter / Setter LIVRE
    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): static
    {
        $this->livre = $livre;
        return $this;
    }
}
