<?php

namespace Src\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $message_id;

    /**
     * @ORM\Column(type="string")
     * @var stinrg
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @var stinrg
     */
    protected $comment;

    /**
     * @ORM\Column(type="datetime")
     * @var datetime
     */
    protected $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="Src\Entities\Message", inversedBy="comment")
     */
    private $message;

    /**
     * getId
     *
     * @return string
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * getId
     *
     * @return void
     */
    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * getId
     *
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->message_id;
    }

    /**
     * getId
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * getComment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * setComment
     *
     * @return void
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * getCreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    /**
     * setCreatedAt
     *
     * @return void
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime("now");
    }
}
