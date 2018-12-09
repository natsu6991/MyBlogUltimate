<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
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
     *
     * @ORM\JoinColumn(name="author")
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string")
     *
     * @Assert\File(mimeTypes={ "image/*" })
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article", cascade={"remove"})
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(\App\Entity\User $author)
    {
        $this->author = $author;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(\Datetime $date)
    {
        $this->date = $date;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function addComment(\App\Entity\Comment $comment)
    {
        $this->comments[] = $comment;
    }

    public function removeComment(\App\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }
}
