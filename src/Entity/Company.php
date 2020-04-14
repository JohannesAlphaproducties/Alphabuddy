<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Assert\Unique()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $employees;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=10, nullable=true)
     */
    private $revenue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billing_address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billing_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billing_town;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $tlId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WorkOrders", mappedBy="company")
     */
    private $workorders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ticket", mappedBy="company")
     */
    private $tickets;

    public function __toString()
    {
        return $this->getName();
    }

    public function __construct()
    {
        $this->workorders = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEmployees(): ?int
    {
        return $this->employees;
    }

    public function setEmployees(?int $employees): self
    {
        $this->employees = $employees;

        return $this;
    }

    public function getRevenue(): ?string
    {
        return $this->revenue;
    }

    public function setRevenue(?string $revenue): self
    {
        $this->revenue = $revenue;

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

    public function getBillingAddress(): ?string
    {
        return $this->billing_address;
    }

    public function setBillingAddress(?string $billing_address): self
    {
        $this->billing_address = $billing_address;
        return $this;
    }

    public function getBillingZip(): ?string
    {
        return $this->billing_zip;
    }

    public function setBillingZip(?string $billing_zip): self
    {
        $this->billing_zip = $billing_zip;

        return $this;
    }

    public function getBillingTown(): ?string
    {
        return $this->billing_town;
    }

    public function setBillingTown(?string $billing_town): self
    {
        $this->billing_town = $billing_town;

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

    public function getTlId(): ?int
    {
        return $this->tlId;
    }

    public function setTlId(int $tlId): self
    {
        $this->tlId = $tlId;

        return $this;
    }

    /**
     * @return Collection|WorkOrders[]
     */
    public function getWorkorders(): Collection
    {
        return $this->workorders;
    }

    public function addWorkorder(WorkOrders $workorder): self
    {
        if (!$this->workorders->contains($workorder)) {
            $this->workorders[] = $workorder;
            $workorder->setCompany($this);
        }

        return $this;
    }

    public function removeWorkorder(WorkOrders $workorder): self
    {
        if ($this->workorders->contains($workorder)) {
            $this->workorders->removeElement($workorder);
            // set the owning side to null (unless already changed)
            if ($workorder->getCompany() === $this) {
                $workorder->setCompany(null);
            }
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
            $ticket->setCompany($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getCompany() === $this) {
                $ticket->setCompany(null);
            }
        }

        return $this;
    }
}
