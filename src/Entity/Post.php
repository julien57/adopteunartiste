<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="post")
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\React", mappedBy="post")
     */
    private $reacts;

    public function __construct()
    {
        $this->photo = new ArrayCollection();
        $this->reacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhoto(): Collection
    {
        return $this->photo;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photo->contains($photo)) {
            $this->photo[] = $photo;
            $photo->setPost($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photo->contains($photo)) {
            $this->photo->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getPost() === $this) {
                $photo->setPost(null);
            }
        }

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

    /**
     * @return Collection|React[]
     */
    public function getReacts(): Collection
    {
        return $this->reacts;
    }

    public function addReact(React $react): self
    {
        if (!$this->reacts->contains($react)) {
            $this->reacts[] = $react;
            $react->setPost($this);
        }

        return $this;
    }

    public function removeReact(React $react): self
    {
        if ($this->reacts->contains($react)) {
            $this->reacts->removeElement($react);
            // set the owning side to null (unless already changed)
            if ($react->getPost() === $this) {
                $react->setPost(null);
            }
        }

        return $this;
    }
}
