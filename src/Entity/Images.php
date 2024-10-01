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
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
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
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

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
