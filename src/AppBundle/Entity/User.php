<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $apiToken;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $cnp;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;


    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TransactionEntity", mappedBy="user", cascade={"persist"})
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return null|Collection|TransactionEntity[]
     */
    public function getTransactions():?Collection
    {
        return $this->transactions;
    }

    /**
     * @param TransactionEntity $transactions
     * @return User
     */
    public function addTransactions($transactions):User
    {
        if (!$this->transactions->contains($transactions)) {
            $this->transactions[] = $transactions;
            $transactions->setUser($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCnp(): ?string
    {
        return $this->cnp;
    }

    /**
     * @param string $cnp
     * @return User
     */
    public function setCnp(string $cnp): User
    {
        $this->cnp = $cnp;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    /**
     * @param string $token
     * @return User
     */
    public function setApiToken(string $token): User
    {
        $this->apiToken = $token;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getSalt()
    {

    }

    public function eraseCredentials()
    {

    }
}
