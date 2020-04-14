<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HoursRepository")
 */
class Hours
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $hours;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WorkOrders", inversedBy="hours")
     */
    private $workorder;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="hours")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(string $hours): self
    {
        $this->hours = $hours;

        return $this;
    }

    public function getWorkorder(): ?WorkOrders
    {
        return $this->workorder;
    }

    public function setWorkorder(?WorkOrders $workorder): self
    {
        $this->workorder = $workorder;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
