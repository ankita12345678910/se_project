<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table("web_book")]
#[ORM\HasLifecycleCallbacks]

#[UniqueEntity(
    fields: ['isbn_no'],
    errorPath: 'isbn_no',
    message: 'This port is already in use on that host.',
)]

class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $isbn_no = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $page_no = null;

    #[ORM\Column(length: 255)]
    private ?string $edition = null;

    #[ORM\Column(length: 255)]
    private ?string $publisher = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 255)]
    private ?string $language = null;

    #[ORM\Column(length: 255)]
    private ?string $binding = null;

    #[ORM\Column(length: 255)]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $available_book = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('Active', 'Deleted')")]
    private ?string $status=null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated = null;

    public function __construct()
    {
        $this->status = 'Active';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbnNo(): ?string
    {
        return $this->isbn_no;
    }

    public function setIsbnNo(string $isbn_no): static
    {
        $this->isbn_no = $isbn_no;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPageNo(): ?string
    {
        return $this->page_no;
    }

    public function setPageNo(string $page_no): static
    {
        $this->page_no = $page_no;

        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(string $edition): static
    {
        $this->edition = $edition;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getBinding(): ?string
    {
        return $this->binding;
    }

    public function setBinding(string $binding): static
    {
        $this->binding = $binding;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAvailableBook(): ?string
    {
        return $this->available_book;
    }

    public function setAvailableBook(string $available_book): static
    {
        $this->available_book = $available_book;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreated(): void
    {
        $this->created = new \DateTimeImmutable();
        $this->setUpdated();
    }

    #[ORM\PreUpdate]
    public function setUpdated(): void
    {
        $this->updated = new \DateTimeImmutable();
    }
}
