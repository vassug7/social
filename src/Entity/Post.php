<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
class Post
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 500)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 500, minMessage:'Title is too short, 2 characters is the minimum')]
    private ?string $text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;
    

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true ,cascade: ['persist'])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'liked')]
    private Collection $likedby;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(length:250,nullable:false)]
    private ?string $postImage=null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;
    
    #[Vich\UploadableField(mapping:"post_images", fileNameProperty:"postImage")]
    private ?File $postImageFile = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likedby = new ArrayCollection();
        $this->created= new DateTime();
        $this->updatedAt = new \DateTimeImmutable();

    
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection<int, comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikedby(): Collection
    {
        return $this->likedby;
    }

    public function addLikedby(User $likedby): static
    {
        if (!$this->likedby->contains($likedby)) {
            $this->likedby->add($likedby);
        }

        return $this;
    }

    public function removeLikedby(User $likedby): static
    {
        $this->likedby->removeElement($likedby);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
    public function getPostImage(): ?string
    {
        return $this->postImage;
    }

    public function setPostImage(?string $postImage): void
    {
        $this->postImage = $postImage;

    }
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function getPostImageFile(): ?File
    {
        return $this->postImageFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setPostImageFile(?File $postImageFile = null): void
    {
        $this->postImageFile = $postImageFile;

        if (null !== $postImageFile) {
            
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    
}
