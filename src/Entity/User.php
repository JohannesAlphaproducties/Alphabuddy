<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
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
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\WorkOrders", mappedBy="mechanic")
     */
    private $workOrders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ticket", mappedBy="user")
     */
    private $tickets;

    /**
     * @Orm\ManyToMany(targetEntity="App\Entity\Ticket", mappedBy="responsible")
     */
    private $ticket_responsibility;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hours", mappedBy="user")
     */
    private $hours;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $subscribedWorkOrder;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $subscribedTicket;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $subscribedResponsibleTicket;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contract;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $subscribedResponsibleWorkOrder;

    public function __construct()
    {
        $this->workOrders = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->hours = new ArrayCollection();
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

    public function __toString()
    {
        return $this->getUsername();
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

    public function setPassword(string $password): self
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|WorkOrders[]
     */
    public function getWorkOrders(): Collection
    {
        return $this->workOrders;
    }

    public function addWorkOrder(WorkOrders $workOrder): self
    {
        if (!$this->workOrders->contains($workOrder)) {
            $this->workOrders[] = $workOrder;
            $workOrder->addMechanic($this);
        }

        return $this;
    }

    public function removeWorkOrder(WorkOrders $workOrder): self
    {
        if ($this->workOrders->contains($workOrder)) {
            $this->workOrders->removeElement($workOrder);
            $workOrder->removeMechanic($this);
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getUser() === $this) {
                $ticket->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Hours[]
     */
    public function getHours(): Collection
    {
        return $this->hours;
    }

    public function addHour(Hours $hour): self
    {
        if (!$this->hours->contains($hour)) {
            $this->hours[] = $hour;
            $hour->setUser($this);
        }

        return $this;
    }

    public function removeHour(Hours $hour): self
    {
        if ($this->hours->contains($hour)) {
            $this->hours->removeElement($hour);
            // set the owning side to null (unless already changed)
            if ($hour->getUser() === $this) {
                $hour->setUser(null);
            }
        }

        return $this;
    }

    public function getSubscribedWorkOrder(): ?bool
    {
        return $this->subscribedWorkOrder;
    }

    public function setSubscribedWorkOrder(?bool $subscribedWorkOrder): self
    {
        $this->subscribedWorkOrder = $subscribedWorkOrder;

        return $this;
    }

    public function getSubscribedTicket(): ?bool
    {
        return $this->subscribedTicket;
    }

    public function setSubscribedTicket(?bool $subscribedTicket): self
    {
        $this->subscribedTicket = $subscribedTicket;

        return $this;
    }

    public function getSubscribedResponsibleTicket(): ?bool
    {
        return $this->subscribedResponsibleTicket;
    }

    public function setSubscribedResponsibleTicket(?bool $subscribedResponsibleTicket): self
    {
        $this->subscribedResponsibleTicket = $subscribedResponsibleTicket;

        return $this;
    }

    public function getContract(): ?string
    {
        return $this->contract;
    }

    public function setContract(?string $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getSubscribedResponsibleWorkOrder(): ?bool
    {
        return $this->subscribedResponsibleWorkOrder;
    }

    public function setSubscribedResponsibleWorkOrder(?bool $subscribedResponsibleWorkOrder): self
    {
        $this->subscribedResponsibleWorkOrder = $subscribedResponsibleWorkOrder;

        return $this;
    }
}
