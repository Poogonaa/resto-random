<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('pseudo', message: 'Ce pseudo est déjà utilisé.')]
#[UniqueEntity('mail', message: 'Cette adresse mail est déjà utilisée.')]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255, unique:true)]
    #[Assert\Length(min: 2, minMessage: 'Votre pseudo doit faire au minimum 2 caractères.')]
    #[Assert\NotBlank(message: 'Vous devez entrer un pseudo.')]
    private ?string $pseudo;

    #[ORM\Column(type: 'string', length: 512)]
    #[Assert\Length(min: 8, minMessage: 'Votre mot de passe doit faire au minimum 8 caractères.')]
    #[Assert\NotBlank(message: 'Vous devez entrer un mot de passe.')]
    private ?string $password;

    #[ORM\Column(type: 'string', length: 412, unique: true)]
    #[Assert\Email(message: '{{ value }} n\'est pas une adresse Email valide.')]
    #[Assert\NotBlank(message: 'Vous devez entrer un email.')]
    private ?string $mail;

    #[ORM\Column(type: 'boolean')]
    private bool $active = false;

    #[ORM\Column(type:"json")]
    private array $roles = [];

    #[ORM\Column(type: 'integer')]
    private ?int $point;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Restaurant::class, orphanRemoval: true)]
    private $Restaurant;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    public function __construct()
    {
        $this->Restaurant = new ArrayCollection();
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): self
    {
        $this->point = $point;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Restaurant>
     */
    public function getRestaurant(): Collection
    {
        return $this->Restaurant;
    }

    public function addRestaurant(Restaurant $restaurant): self
    {
        if (!$this->Restaurant->contains($restaurant)) {
            $this->Restaurant[] = $restaurant;
            $restaurant->setUser($this);
        }

        return $this;
    }

    public function removeRestaurant(Restaurant $restaurant): self
    {
        if ($this->Restaurant->removeElement($restaurant)) {
            // set the owning side to null (unless already changed)
            if ($restaurant->getUser() === $this) {
                $restaurant->setUser(null);
            }
        }

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->pseudo;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
