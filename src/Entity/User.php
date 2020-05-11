<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="Cette adresse mail existe déjà")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo existe déjà, veuillez en choisir un autre")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank(message="Veuillez renseigner votre adresse mail")
     * @Assert\Email(message="L'adresse mail renseignée n'est pas valide")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Veuillez définir un mot de passe")
     * @Assert\Length(min=6, minMessage="Veuillez choisir au moins 6 caractères pour votre mot de passe")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="Veuillez renseigner un pseudo")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank(message="Veuillez renseigner votre date de naissance")
     */
    private $birthAt;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     *
     * @Assert\NegativeOrZero(message="Le numérode téléphone renseigné n'est pas valide")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     */
    private $doll;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DomainArtist")
     * @ORM\JoinColumn(nullable=false)
     */
    private $domainArtist;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\React", inversedBy="users")
     */
    private $react;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

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
     * @ORM\Column(type="integer")
     */
    private $nbVisit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Adopt", mappedBy="userTo", orphanRemoval=true)
     */
    private $userFrom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Adopt", mappedBy="userFrom", orphanRemoval=true)
     */
    private $adopts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Competence", mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $competences;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Service", mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentPost", mappedBy="user")
     */
    private $commentPosts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="author", orphanRemoval=true)
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="members")
     */
    private $userGroups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Messaging", mappedBy="sendTo")
     */
    private $senderTo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Messaging", mappedBy="sendFor")
     */
    private $senderFor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $subscribedAt;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->doll = 0;
        $this->react = new ArrayCollection();
        $this->userFrom = new ArrayCollection();
        $this->adopts = new ArrayCollection();
        $this->competences = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->commentPosts = new ArrayCollection();
        $this->nbVisit = 0;
        $this->groups = new ArrayCollection();
        $this->userGroups = new ArrayCollection();
        $this->senderTo = new ArrayCollection();
        $this->senderFor = new ArrayCollection();
        $this->subscribedAt = new \DateTime();
    }

    public function computeSlug(SluggerInterface $slugger)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this->pseudo)->lower();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getBirthAt(): ?\DateTimeInterface
    {
        return $this->birthAt;
    }

    public function setBirthAt(?\DateTimeInterface $birthAt): self
    {
        $this->birthAt = $birthAt;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

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

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

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

    public function getDoll(): ?int
    {
        return $this->doll;
    }

    public function setDoll(int $doll): self
    {
        $this->doll = $doll;

        return $this;
    }

    public function getDomainArtist(): ?DomainArtist
    {
        return $this->domainArtist;
    }

    public function setDomainArtist(?DomainArtist $domainArtist): self
    {
        $this->domainArtist = $domainArtist;

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
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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

    public function getNbVisit(): ?int
    {
        return $this->nbVisit;
    }

    public function setNbVisit(int $nbVisit): self
    {
        $this->nbVisit = $nbVisit;

        return $this;
    }

    /**
     * @return Collection|React[]
     */
    public function getReact(): Collection
    {
        return $this->react;
    }

    public function addReact(React $react): self
    {
        if (!$this->react->contains($react)) {
            $this->react[] = $react;
        }

        return $this;
    }

    public function removeReact(React $react): self
    {
        if ($this->react->contains($react)) {
            $this->react->removeElement($react);
        }

        return $this;
    }

    /**
     * @return Collection|Adopt[]
     */
    public function getUserFrom(): Collection
    {
        return $this->userFrom;
    }

    public function addUserFrom(Adopt $userFrom): self
    {
        if (!$this->userFrom->contains($userFrom)) {
            $this->userFrom[] = $userFrom;
            $userFrom->setUserTo($this);
        }

        return $this;
    }

    public function removeUserFrom(Adopt $userFrom): self
    {
        if ($this->userFrom->contains($userFrom)) {
            $this->userFrom->removeElement($userFrom);
            // set the owning side to null (unless already changed)
            if ($userFrom->getUserTo() === $this) {
                $userFrom->setUserTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Adopt[]
     */
    public function getAdopts(): Collection
    {
        return $this->adopts;
    }

    public function addAdopt(Adopt $adopt): self
    {
        if (!$this->adopts->contains($adopt)) {
            $this->adopts[] = $adopt;
            $adopt->setUserFrom($this);
        }

        return $this;
    }

    public function removeAdopt(Adopt $adopt): self
    {
        if ($this->adopts->contains($adopt)) {
            $this->adopts->removeElement($adopt);
            // set the owning side to null (unless already changed)
            if ($adopt->getUserFrom() === $this) {
                $adopt->setUserFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Competence[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->setUser($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        if ($this->competences->contains($competence)) {
            $this->competences->removeElement($competence);
            // set the owning side to null (unless already changed)
            if ($competence->getUser() === $this) {
                $competence->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setUser($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getUser() === $this) {
                $service->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommentPost[]
     */
    public function getCommentPosts(): Collection
    {
        return $this->commentPosts;
    }

    public function addCommentPost(CommentPost $commentPost): self
    {
        if (!$this->commentPosts->contains($commentPost)) {
            $this->commentPosts[] = $commentPost;
            $commentPost->setUser($this);
        }

        return $this;
    }

    public function removeCommentPost(CommentPost $commentPost): self
    {
        if ($this->commentPosts->contains($commentPost)) {
            $this->commentPosts->removeElement($commentPost);
            // set the owning side to null (unless already changed)
            if ($commentPost->getUser() === $this) {
                $commentPost->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setAuthor($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getAuthor() === $this) {
                $group->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    public function addUserGroup(Group $userGroup): self
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups[] = $userGroup;
            $userGroup->addMember($this);
        }

        return $this;
    }

    public function removeUserGroup(Group $userGroup): self
    {
        if ($this->userGroups->contains($userGroup)) {
            $this->userGroups->removeElement($userGroup);
            $userGroup->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection|Messaging[]
     */
    public function getSenderTo(): Collection
    {
        return $this->senderTo;
    }

    public function addSenderTo(Messaging $senderTo): self
    {
        if (!$this->senderTo->contains($senderTo)) {
            $this->senderTo[] = $senderTo;
            $senderTo->setSendTo($this);
        }

        return $this;
    }

    public function removeSenderTo(Messaging $senderTo): self
    {
        if ($this->senderTo->contains($senderTo)) {
            $this->senderTo->removeElement($senderTo);
            // set the owning side to null (unless already changed)
            if ($senderTo->getSendTo() === $this) {
                $senderTo->setSendTo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Messaging[]
     */
    public function getSenderFor(): Collection
    {
        return $this->senderFor;
    }

    public function addSenderFor(Messaging $senderFor): self
    {
        if (!$this->senderFor->contains($senderFor)) {
            $this->senderFor[] = $senderFor;
            $senderFor->setSendFor($this);
        }

        return $this;
    }

    public function removeSenderFor(Messaging $senderFor): self
    {
        if ($this->senderFor->contains($senderFor)) {
            $this->senderFor->removeElement($senderFor);
            // set the owning side to null (unless already changed)
            if ($senderFor->getSendFor() === $this) {
                $senderFor->setSendFor(null);
            }
        }

        return $this;
    }

    public function getSubscribedAt(): ?\DateTimeInterface
    {
        return $this->subscribedAt;
    }

    public function setSubscribedAt(\DateTimeInterface $subscribedAt): self
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }
}
