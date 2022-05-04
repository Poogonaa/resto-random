<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Vous devez entrer le nom d'un restaurant.")]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 512)]
    private ?string $picture;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Vous devez entrer la ville.')]
    private ?string $city;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Vous devez entrer le code postal.')]
    private ?int $postal_code;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Vous devez entrer le numéro de rue du restaurant (entrez 0 si vous ne le connaissez pas).')]
    private ?int $number;

    #[ORM\Column(type: 'string', length: 512)]
    #[Assert\NotBlank(message: 'Vous devez entrer le nom de rue.')]
    private ?string $street;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    private ?string $complement;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Restaurant')]
    #[ORM\JoinColumn(referencedColumnName:'pseudo', nullable: true)]
    private ?User $user;

    #[ORM\ManyToMany(targetEntity: Category::class)]
    #[Assert\Count(
        min: "1",
        minMessage: "Vous devez sélectionner au moins une catégorie."
    )]
    private $Category;

    public function __construct()
    {
        $this->Category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postal_code;
    }

    public function setPostalCode(int $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): Collection
    {
        return $this->Category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->Category->contains($category)) {
            $this->Category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->Category->removeElement($category);

        return $this;
    }

    public function getPicturePath(){
        return 'upload/'.$this->getPicture();
    }
}
