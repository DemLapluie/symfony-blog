<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un utilisateur avec cet email")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, name="lastname")
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(max="100",
     *     maxMessage="Le nom ne doit pas faire plus de {{ limit }} caractères" )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(max="100",
     *     maxMessage="Le prénom ne doit pas faire plus de {{ limit }} caractères" )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * mot de passe en clair pour interagir avec le formulaire
     * @var string
     * @Assert\NotBlank(message="mdp obligatoire")
     */
    private $plainpassword;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="L'email ne doit pas être vide")
     * @Assert\Email(message="L'email n'est pas valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $role = 'ROLE_USER';

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPlainpassword(): ?string
    {
        return $this->plainpassword;
    }

    public function setPlainpassword(string $plainpassword): User
    {
        $this->plainpassword = $plainpassword;
        return $this;
    }

    /**
     * Transforme un objet User en chaîne de caractère
     * @return string
     */
    public function serialize() :string
    {
        return serialize([
            $this->id,
            $this->name,
            $this->firstname,
            $this->email,
            $this->password
        ]);
    }

    /**
     * Transforme une chaîne générée par serialize en objet user
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->firstname,
            $this->email,
            $this->password
            ) = unserialize($serialized);
    }

    /**
     * Rôle sous forme d'un array
     * @return array
     */
    public function getRoles()
    {
        return [$this->role];
    }
    public function getSalt()
    {
        return null;
    }

    /**
     * quel attribut va servir d'identifiant
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {

    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->name;
    }
}
