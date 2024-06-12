<?php

namespace App\Entity;

use App\Repository\IndexesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexesRepository::class)]
class Indexes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $production = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $resale = null;

    #[ORM\ManyToOne(inversedBy: 'indexes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $fk_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduction(): ?string
    {
        return $this->production;
    }

    public function setProduction(string $production): static
    {
        $this->production = str_replace(',', '.', $production);

        return $this;
    }

    public function getResale(): ?string
    {
        return $this->resale;
    }

    public function setResale(string $resale): static
    {
        $this->resale = str_replace(',', '.', $resale);

        return $this;
    }

    public function getFkUser(): ?Users
    {
        return $this->fk_user;
    }

    public function setFkUser(?Users $fk_user): static
    {
        $this->fk_user = $fk_user;

        return $this;
    }
}
