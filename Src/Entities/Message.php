<?php
namespace Src\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="messages")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var stinrg
     */
    protected $author;

    /**
     * @ORM\Column(type="string")
     * @var stinrg
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @var stinrg
     */
    protected $content;

    /**
     * @ORM\Column(type="datetime")
     * @var datetime
     */
    protected $created_at;

    /**
     * @ORM\OneToMany(targetEntity="Src\Entities\Comment", mappedBy="message", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
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
     * getAuthor
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * setAuthor
     *
     * @return void
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * getTitle
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * setTitle
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * getContent
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * setContent
     *
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
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
