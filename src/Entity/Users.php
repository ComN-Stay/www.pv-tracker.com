<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 80)]
    private ?string $firstname = null;

    #[ORM\Column(length: 80)]
    private ?string $lastname = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $mobile = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genders $fk_gender = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserStatuses $fk_status = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    private ?string $zip_code = null;

    #[ORM\Column(length: 100)]
    private ?string $town = null;

    #[ORM\ManyToOne]
    private ?Countries $fk_country = null;

    /**
     * @var Collection<int, Energy>
     */
    #[ORM\OneToMany(targetEntity: Energy::class, mappedBy: 'fk_user', orphanRemoval: true)]
    private Collection $energies;

    /**
     * @var Collection<int, Months>
     */
    #[ORM\OneToMany(targetEntity: Months::class, mappedBy: 'fk_user', orphanRemoval: true)]
    private Collection $months;

    /**
     * @var Collection<int, Prices>
     */
    #[ORM\OneToMany(targetEntity: Prices::class, mappedBy: 'fk_user', orphanRemoval: true)]
    private Collection $prices;

    /**
     * @var Collection<int, Indexes>
     */
    #[ORM\OneToMany(targetEntity: Indexes::class, mappedBy: 'fk_user', orphanRemoval: true)]
    private Collection $indexes;

    /**
     * @var Collection<int, Systems>
     */
    #[ORM\OneToMany(targetEntity: Systems::class, mappedBy: 'fk_user', orphanRemoval: true)]
    private Collection $systems;

    public function __construct()
    {
        $this->energies = new ArrayCollection();
        $this->months = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->indexes = new ArrayCollection();
        $this->systems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getFkGender(): ?Genders
    {
        return $this->fk_gender;
    }

    public function setFkGender(?Genders $fk_gender): static
    {
        $this->fk_gender = $fk_gender;

        return $this;
    }

    public function getFkStatus(): ?UserStatuses
    {
        return $this->fk_status;
    }

    public function setFkStatus(?UserStatuses $fk_status): static
    {
        $this->fk_status = $fk_status;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): static
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(string $town): static
    {
        $this->town = $town;

        return $this;
    }

    public function getFkCountry(): ?Countries
    {
        return $this->fk_country;
    }

    public function setFkCountry(?Countries $fk_country): static
    {
        $this->fk_country = $fk_country;

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
            $energy->setFkUser($this);
        }

        return $this;
    }

    public function removeEnergy(Energy $energy): static
    {
        if ($this->energies->removeElement($energy)) {
            // set the owning side to null (unless already changed)
            if ($energy->getFkUser() === $this) {
                $energy->setFkUser(null);
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
            $month->setFkUser($this);
        }

        return $this;
    }

    public function removeMonth(Months $month): static
    {
        if ($this->months->removeElement($month)) {
            // set the owning side to null (unless already changed)
            if ($month->getFkUser() === $this) {
                $month->setFkUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prices>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Prices $price): static
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setFkUser($this);
        }

        return $this;
    }

    public function removePrice(Prices $price): static
    {
        if ($this->prices->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getFkUser() === $this) {
                $price->setFkUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Indexes>
     */
    public function getIndexes(): Collection
    {
        return $this->indexes;
    }

    public function addIndex(Indexes $index): static
    {
        if (!$this->indexes->contains($index)) {
            $this->indexes->add($index);
            $index->setFkUser($this);
        }

        return $this;
    }

    public function removeIndex(Indexes $index): static
    {
        if ($this->indexes->removeElement($index)) {
            // set the owning side to null (unless already changed)
            if ($index->getFkUser() === $this) {
                $index->setFkUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Systems>
     */
    public function getSystems(): Collection
    {
        return $this->systems;
    }

    public function addSystem(Systems $system): static
    {
        if (!$this->systems->contains($system)) {
            $this->systems->add($system);
            $system->setFkUser($this);
        }

        return $this;
    }

    public function removeSystem(Systems $system): static
    {
        if ($this->systems->removeElement($system)) {
            // set the owning side to null (unless already changed)
            if ($system->getFkUser() === $this) {
                $system->setFkUser(null);
            }
        }

        return $this;
    }
}
