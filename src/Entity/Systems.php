<?php

namespace App\Entity;

use App\Repository\SystemsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemsRepository::class)]
class Systems
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $system_id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $refresh_token = null;

    #[ORM\ManyToOne(inversedBy: 'systems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $fk_user = null;

    /**
     * @var Collection<int, Energy>
     */
    #[ORM\OneToMany(targetEntity: Energy::class, mappedBy: 'fk_system', orphanRemoval: true)]
    private Collection $energies;

    /**
     * @var Collection<int, Months>
     */
    #[ORM\OneToMany(targetEntity: Months::class, mappedBy: 'fk_system', orphanRemoval: true)]
    private Collection $months;

    public function __construct()
    {
        $this->energies = new ArrayCollection();
        $this->months = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSystemId(): ?string
    {
        return $this->system_id;
    }

    public function setSystemId(?string $system_id): static
    {
        $this->system_id = $system_id;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(?string $refresh_token): static
    {
        $this->refresh_token = $refresh_token;

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

    /**
     * @return Collection<int, Energy>
     */
    public function getEnergies(): Collection
    {
        return $this->energies;
    }

    public function addEnergy(Energy $energy): static
    {
        if (!$this->energies->contains($energy)) {
            $this->energies->add($energy);
            $energy->setFkSystem($this);
        }

        return $this;
    }

    public function removeEnergy(Energy $energy): static
    {
        if ($this->energies->removeElement($energy)) {
            // set the owning side to null (unless already changed)
            if ($energy->getFkSystem() === $this) {
                $energy->setFkSystem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Months>
     */
    public function getMonths(): Collection
    {
        return $this->months;
    }

    public function addMonth(Months $month): static
    {
        if (!$this->months->contains($month)) {
            $this->months->add($month);
            $month->setFkSystem($this);
        }

        return $this;
    }

    public function removeMonth(Months $month): static
    {
        if ($this->months->removeElement($month)) {
            // set the owning side to null (unless already changed)
            if ($month->getFkSystem() === $this) {
                $month->setFkSystem(null);
            }
        }

        return $this;
    }
}
