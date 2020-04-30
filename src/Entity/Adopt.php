<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdoptRepository")
 */
class Adopt
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userFrom")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="adopts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFrom;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid;

    public function __construct()
    {
        $this->isValid = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserTo(): ?User
    {
        return $this->userTo;
    }

    public function setUserTo(?User $userTo): self
    {
        $this->userTo = $userTo;

        return $this;
    }

    public function getUserFrom(): ?User
    {
        return $this->userFrom;
    }

    public function setUserFrom(?User $userFrom): self
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }
}
