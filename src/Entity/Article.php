<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="le titre est obligatoire")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le contenu est obligatoire")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="la date est obligatoire")
     */
    private $publicationDate;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="la categorie est obligatoire")
     */
    private $category;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     *
     */
    private $author;

    /**
     * @ORM\Column(type="string", nullable=true)
     * // le fichier uploadé doit être une image
     * @Assert\Image(maxSize="1M", maxSizeMessage="Le fichier ne doit pas exceder 1Mo", mimeTypesMessage="Le fichier n'est pas une image")
     */
    private $image;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     * POur choisir l'ordre de tri par défaut
     * @ORM\OrderBy({"publicationDate" = "DESC"})
     */
    private $comments;
    
    public function __construct()
    {
        $this->publicationDate =new  \DateTime();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Collection $comments
     * @return Article
     */
    public function setComments(Collection $comments): Article
    {
        $this->comments = $comments;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    /**
     * @param \DateTimeInterface $publicationDate
     * @return Article
     * @var datetime
     */
    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Article
     */
    public function setCategory(Category $category): Article
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Article
     */
    public function setAuthor(User $author): Article
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Article
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }



}
