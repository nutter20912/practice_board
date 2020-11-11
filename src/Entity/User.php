<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Version;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Version
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="account",
     *  options={"comment"="帳號"},
     *  type="string",
     *  length=30,
     *  unique=true
     * )
     */
    private $account;

    /**
     * @ORM\Column(
     *  name="cash",
     *  options={"comment"="額度"},
     * type="integer"
     * )
     */
    private $cash;

    /**
     * @ORM\Column(
     *  name="created_at",
     *  options={"comment"="新增時間"},
     *  type="datetime"
     * )
     */
    private $created_at;

    /**
     * @ORM\Column(
     *  name="updated_at",
     *  options={"comment"="更新時間"},
     *  type="datetime",
     *  columnDefinition="DATETIME on update CURRENT_TIMESTAMP"
     * )
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(
     *  targetEntity="App\Entity\CashRecords",
     *  mappedBy="user",
     *  orphanRemoval=true
     * )
     */
    private $cashRecords;

    public function __construct()
    {
        $this->cashRecords = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount($account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getUsername()
    {
        return (string) $this->account;
    }

    public function getCash()
    {
        return $this->cash;
    }

    public function setCash($cash): self
    {
        $this->cash = $cash;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCashRecords(): Collection
    {
        return $this->cashRecords;
    }
}
