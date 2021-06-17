<?php

namespace App\Entity;

use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as ProjectAsserts;

/**
 * @ORM\Entity(repositoryClass=App\Repository\ProjectRepository::class)
 * @ORM\Table(indexes={@ORM\Index(name="project_deleted_idx", columns={"deleted"})})
 * @ProjectAsserts\ProjectClass
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank]
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="projects")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="projects")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="project")
     */
    private $tasks;

    /**
     * The status is dependent/computed on the task statuses and is not a database column for the project table/entity
     *
     * @var \App\Entity\ProjectStatus
     */
    private ?\App\Entity\ProjectStatus $status = null;

    /**
     * The duration is dependent/computed on the task durations and is not a database column for the project table/entity
     *
     * @var \DateInterval
     */
    private ?\DateInterval $duration = null;

    /**
     * @ORM\Column(type="smallint", options={"default"=0})
     */
    private $deleted = 0;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(?ProjectStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDeleted(): ?int
    {
        return $this->deleted;
    }

    public function setDeleted(int $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
