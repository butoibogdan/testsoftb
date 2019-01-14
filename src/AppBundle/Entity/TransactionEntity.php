<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionEntity
 *
 * @ORM\Table(name="transaction_entity")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionEntityRepository")
 */
class TransactionEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="id")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId():int
    {
        return $this->id;
    }

    /**
     *
     * @param string $amount
     *
     * @return TransactionEntity
     */
    public function setAmount($amount): TransactionEntity
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     *
     * @param \DateTime $date
     *
     * @return TransactionEntity
     */
    public function setDate($date): TransactionEntity
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param User $user
     *
     * @return TransactionEntity
     */
    public function setUser($user): TransactionEntity
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId.
     * @return User
     */
    public function getUser():User
    {
        return $this->user;
    }
}
