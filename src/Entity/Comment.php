<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @ORM\Table(name="comment")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(
     *  name="message_id",
     *  options={"comment"="訊息id"},
     *  type="integer"
     * )
     */
    private $message_id;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="name",
     *  options={"comment"="名字"},
     *  type="string",
     *  length=30
     * )
     */
    private $name;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(
     *  name="content",
     *  options={"comment"="內容"},
     *  type="string",
     *  length=255
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
     *  type="datetime",
     *  columnDefinition="DATETIME on update CURRENT_TIMESTAMP"
     * )
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(
     *  targetEntity="App\Entity\Message",
     *  inversedBy="comment"
     * )
     */
    private $message;

    public function getId()
    {
        return $this->id;
    }

    public function getMessageId()
    {
        return $this->message_id;
    }

    public function setMessageId($message_id): self
    {
        $this->message_id = $message_id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

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

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }
}
