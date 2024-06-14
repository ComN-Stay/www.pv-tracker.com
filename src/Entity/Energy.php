<?php

namespace App\Entity;

use App\Repository\EnergyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnergyRepository::class)]
class Energy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $production = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $import = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $export = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $self = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $import_cost = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $export_income = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $savings = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $balance = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $consumption = null;

    #[ORM\ManyToOne(inversedBy: 'energies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $fk_user = null;

    #[ORM\ManyToOne(inversedBy: 'energies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Systems $fk_system = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getProduction(): ?string
    {
        return str_replace(',', '.', $this->production);
    }

    public function setProduction(string $production): static
    {
        $this->production = str_replace(',', '.', $production);

        return $this;
    }

    public function getImport(): ?string
    {
        return str_replace(',', '.', $this->import);
    }

    public function setImport(string $import): static
    {
        $this->import = str_replace(',', '.', $import);

        return $this;
    }

    public function getExport(): ?string
    {
        return str_replace(',', '.', $this->export);
    }

    public function setExport(?string $export): static
    {
        $this->export = str_replace(',', '.', $export);

        return $this;
    }

    public function getSelf(): ?string
    {
        return str_replace(',', '.', $this->self);
    }

    public function setSelf(?string $self): static
    {
        $this->self = str_replace(',', '.', $self);

        return $this;
    }

    public function getImportCost(): ?string
    {
        return str_replace(',', '.', $this->import_cost);
    }

    public function setImportCost(?string $import_cost): static
    {
        $this->import_cost = str_replace(',', '.', $import_cost);

        return $this;
    }

    public function getExportIncome(): ?string
    {
        return str_replace(',', '.', $this->export_income);
    }

    public function setExportIncome(?string $export_income): static
    {
        $this->export_income = str_replace(',', '.', $export_income);

        return $this;
    }

    public function getSavings(): ?string
    {
        return str_replace(',', '.', $this->savings);
    }

    public function setSavings(?string $savings): static
    {
        $this->savings = str_replace(',', '.', $savings);

        return $this;
    }

    public function getBalance(): ?string
    {
        return str_replace(',', '.', $this->balance);
    }

    public function setBalance(?string $balance): static
    {
        $this->balance = str_replace(',', '.', $balance);

        return $this;
    }

    public function getConsumption(): ?string
    {
        return str_replace(',', '.', $this->consumption);
    }

    public function setConsumption(?string $consumption): static
    {
        $this->consumption = str_replace(',', '.', $consumption);

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

    public function getFkSystem(): ?Systems
    {
        return $this->fk_system;
    }

    public function setFkSystem(?Systems $fk_system): static
    {
        $this->fk_system = $fk_system;

        return $this;
    }
}
