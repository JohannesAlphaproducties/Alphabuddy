<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkOrdersRepository")
 */
class WorkOrders
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="workOrders")
     */
    private $mechanic;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="workorders")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hours", mappedBy="workorder", cascade={"remove"})
     */
    private $hours;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $signedBy;

    public function __construct()
    {
        $this->mechanic = new ArrayCollection();
        $this->time = new \DateTime("now", "Europe/Amsterdam");
        $this->hours = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->titel;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitel(): ?string
    {
        return $this->titel;
    }

    public function setTitel(string $titel): self
    {
        $this->titel = $titel;

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

    /**
     * @return Collection|User[]
     */
    public function getMechanic(): Collection
    {
        return $this->mechanic;
    }

    public function addMechanic(User $mechanic): self
    {
        if (!$this->mechanic->contains($mechanic)) {
            $this->mechanic[] = $mechanic;
        }

        return $this;
    }

    public function removeMechanic(User $mechanic): self
    {
        if ($this->mechanic->contains($mechanic)) {
            $this->mechanic->removeElement($mechanic);
        }

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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
            $hour->setWorkorder($this);
        }

        return $this;
    }

    public function removeHour(Hours $hour): self
    {
        if ($this->hours->contains($hour)) {
            $this->hours->removeElement($hour);
            // set the owning side to null (unless already changed)
            if ($hour->getWorkorder() === $this) {
                $hour->setWorkorder(null);
            }
        }

        return $this;
    }

    public function getSignedBy(): ?string
    {
        return $this->signedBy;
    }

    public function setSignedBy(?string $signedBy): self
    {
        $this->signedBy = $signedBy;

        return $this;
    }
}
