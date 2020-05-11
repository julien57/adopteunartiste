<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Image()
     */
    private $cover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Image()
     */
    private $avatar;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Email(message="Adresse mail du groupe non valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(message="URL non valide")
     */
    private $website;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Veuillez donner un nom au groupe")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="userGroups")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="userGroup")
     */
    private $posts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(message="L'URL renseignée n'est pas valide")
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(message="L'URL renseignée n'est pas valide")
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(message="L'URL renseignée n'est pas valide")
     */
    private $youtube;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(message="L'URL renseignée n'est pas valide")
     */
    private $instagram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url(message="L'URL renseignée n'est pas valide")
     */
    private $twitch;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbViews;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->members = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->nbViews = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUserGroup($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUserGroup() === $this) {
                $post->setUserGroup(null);
            }
        }

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    public function setTwitch(?string $twitch): self
    {
        $this->twitch = $twitch;

        return $this;
    }

    public function isContainUser(User $user)
    {
        if ($this->members->contains($user)) {
            return true;
        }

        return false;
    }

    public function getNbViews(): ?int
    {
        return $this->nbViews;
    }

    public function setNbViews(?int $nbViews): self
    {
        $this->nbViews = $nbViews;

        return $this;
    }
}
