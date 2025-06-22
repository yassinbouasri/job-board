<?php

namespace App\Entity;

use App\Repository\JobAlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobAlertRepository::class)]
class JobAlert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'jobAlerts')]
    private User $usr;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keyword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tags = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frequency = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastNotifiedAt = null;

    #[ORM\Column]
    private ?bool $isRead = false;

    /**
     * @var Collection<int, Job>
     */
    #[ORM\OneToMany(targetEntity: Job::class, mappedBy: 'jobAlert', fetch: 'EAGER')]
    private Collection $Jobs;

    public function __construct()
    {
        $this->usr = new User();
        $this->Jobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsr(): User
    {
        return $this->usr;
    }

    public function addUsr(User $usr): static
    {
        $this->usr = $usr;

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags ?? [];

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getLastNotifiedAt(): ?\DateTimeImmutable
    {
        return $this->lastNotifiedAt;
    }

    public function setLastNotifiedAt(?\DateTimeImmutable $lastNotifiedAt): static
    {
        $this->lastNotifiedAt = $lastNotifiedAt;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->Jobs;
    }

    public function addJob(Job $job): static
    {
        if (!$this->Jobs->contains($job)) {
            $this->Jobs->add($job);
            $job->setJobAlert($this);
        }

        return $this;
    }

    public function addJobs(array $jobs): static
    {
        foreach ($jobs as $job) {
            $this->addJob($job);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->Jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getJobAlert() === $this) {
                $job->setJobAlert(null);
            }
        }

        return $this;
    }
}
