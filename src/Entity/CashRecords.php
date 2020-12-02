<?php

namespace App\Entity;

use App\Repository\CashRecordsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CashRecordsRepository::class)
 * @ORM\Table(name="cash_records")
 */
class CashRecords
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(
     *  name="user_id",
     *  options={"comment"="帳號id"},
     *  type="integer"
     * )
     */
    private $user_id;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="operator",
     *  options={"comment"="操作者"},
     *  type="string",
     *  length=50
     * )
     */
    private $operator;

    /**
     * @ORM\Column(
     *  name="diff",
     *  options={"comment"="異動額度"},
     *  type="decimal",
     *  precision=10,
     *  scale=3
     * )
     */
    private $diff;

    /**
     * @ORM\Column(
     *  name="current",
     *  options={"comment"="異動後額度"},
     *  type="decimal",
     *  precision=10,
     *  scale=3
     * )
     */
    private $current;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="ip",
     *  options={"comment"="ip"},
     *  type="string",
     *  length=255
     * )
     */
    private $ip;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="created_at",
     *  options={"comment"="新增時間"},
     *  type="datetime"
     * )
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(
     *  targetEntity="App\Entity\User",
     *  inversedBy="cashRecords"
     * )
     */
    private $user;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function setOperator($operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function getDiff()
    {
        return $this->diff;
    }

    public function setDiff($diff): self
    {
        $this->diff = $diff;

        return $this;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($current): self
    {
        $this->current = $current;

        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip): self
    {
        $this->ip = $ip;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
