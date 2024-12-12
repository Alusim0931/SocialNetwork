<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column]
    private ?int $numLikes = null;

    #[ORM\Column]
    private ?int $numViews = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Hashtag::class, mappedBy: 'posts')]
    private Collection $hashtags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->datetime = new DateTime();
        $this->numLikes = 0;
        $this->numViews = 0;
        $this->hashtags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function getDatetimeFormated(): string
    {
        return $this->datetime->format('Y-m-d H:i');
    }

    public function getNumLikes(): ?int
    {
        return $this->numLikes;
    }

    public function setNumLikes(int $numLikes): static
    {
        $this->numLikes = $numLikes;

        return $this;
    }

    public function getNumViews(): ?int
    {
        return $this->numViews;
    }

    public function setNumViews(int $numViews): static
    {
        $this->numViews = $numViews;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function like(): void
    {
        if ($this->user) {
            if ($this->user->getLikedPosts()->contains($this)) {
                $this->numLikes--;
                $this->user->removeLikedPost($this);
            } else {
                $this->numLikes++;
                $this->user->addLikedPost($this);
            }
        }
    }

    public function isLikedByUser(User $user): bool
    {
        return $this->user && $this->user->getLikedPosts()->contains($this);
    }

    public function addView(): void
    {
        $this->numViews++;
    }

    /**
     * @return Collection<int, Hashtag>
     */
    public function getHashtags(): Collection
    {
        return $this->hashtags;
    }

    public function addHashtag(Hashtag $hashtag): static
    {
        if (!$this->hashtags->contains($hashtag)) {
            $this->hashtags->add($hashtag);
            $hashtag->addPost($this);
        }

        return $this;
    }

    public function removeHashtag(Hashtag $hashtag): static
    {
        if ($this->hashtags->removeElement($hashtag)) {
            $hashtag->removePost($this);
        }

        return $this;
    }
}
