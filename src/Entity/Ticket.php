<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TicketRepository")
 * @Orm\Table(name="ticket")
 * @Orm\HasLifecycleCallbacks
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tickets")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="ticket_responsibility")
     */
    private $responsible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="tickets")
     */
    private $company;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;


    /**
     * @var datetime $created_at
     *
     * @Orm\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @var datetime $updated_at
     *
     * @Orm\Column(type="datetime", nullable = true)
     */
    protected $updated_at;

    /**
     * @orm\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
    }

    /**
     * @Orm\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated_at = new \DateTime("now");
    }

    public function __construct()
    {
        $this->responsible = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|User[]
     */
    public function getResponsible(): Collection
    {
        return $this->responsible;
    }

    public function addResponsible(User $responsible): self
    {
        if (!$this->responsible->contains($responsible)) {
            $this->responsible[] = $responsible;
        }

        return $this;
    }

    public function removeResponsible(User $responsible): self
    {
        if ($this->responsible->contains($responsible)) {
            $this->responsible->removeElement($responsible);
        }

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
