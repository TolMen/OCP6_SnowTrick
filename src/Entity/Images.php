<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $imgURL = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")] // Ajout de "onDelete: CASCADE"
    private ?Trick $id_trick = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImgURL(): ?string
    {
        return $this->imgURL;
    }

    public function setImgURL(string $imgURL): static
    {
        $this->imgURL = $imgURL;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateCreated(\DateTimeInterface $dateAdd): static
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getIdTrick(): ?Trick
    {
        return $this->id_trick;
    }

    public function setIdTrick(?Trick $id_trick): static
    {
        $this->id_trick = $id_trick;

        return $this;
    }
}
