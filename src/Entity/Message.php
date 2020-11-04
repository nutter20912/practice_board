<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @ORM\Table(name="message")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="author",
     *  options={"comment"="作者"},
     *  type="string",
     *  length=30
     * )
     */
    private $author;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="title",
     *  options={"comment"="標題"},
     *  type="string",
     *  length=50
     * )
     */
    private $title;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="content",
     *  options={"comment"="內容"},
     *  type="text"
     * )
     */
    private $content;

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
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="updated_at",
     *  options={"comment"="更新時間"},
     *  type="datetime"
     * )
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(
     *  targetEntity="App\Entity\Comment",
     *  mappedBy="message",
     *  orphanRemoval=true
     * )
     */
    private $comment;

    public function __construct()
    {
        $this->comment = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

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

    public function getComment(): Collection
    {
        return $this->comment;
    }
}
