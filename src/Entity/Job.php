<?php

namespace App\Entity;

use App\Enums\ExperienceEnum;
use App\Enums\JobTypeEnum;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index('IDX___job___title', fields: ['title'])]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(0)]
    private ?int $salary = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $tags = null;

    #[ORM\Column(type: 'datetime_immutable',nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'jobs')]
    private ?user $createdBy = null;

    /**
     * @var Collection<int, Application>
     */
    #[ORM\OneToMany(targetEntity: Application::class, mappedBy: 'job', orphanRemoval: true)]
    private Collection $applications;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true, enumType: JobTypeEnum::class)]
    private ?array $jobType = null;

    #[ORM\Column(nullable: true, enumType: ExperienceEnum::class)]
    private ?ExperienceEnum $experience = null;

    /**
     * @var Collection<int, Bookmark>
     */
    #[ORM\OneToMany(targetEntity: Bookmark::class, mappedBy: 'job')]
    private Collection $bookmarks;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(?int $salary): static
    {
        $this->salary = $salary;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getCreatedBy(): ?user
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?user $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setJob($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getJob() === $this) {
                $application->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return JobTypeEnum[]|null
     */
    public function getJobType(): ?array
    {
        return $this->jobType;
    }

    public function setJobType(?array $jobType): static
    {
        $this->jobType = $jobType;

        return $this;
    }

    public function getExperience(): ?ExperienceEnum
    {
        return $this->experience;
    }

    public function setExperience(?ExperienceEnum $experience): static
    {
        $this->experience = $experience;

        return $this;
    }
    public function __toString(): string
    {
        return "Job #". $this->getId() .": ". u($this->getTitle())->truncate(strlen($this->getTitle()) / 4, '...');
    }

    /**
     * @return Collection<int, Bookmark>
     */
    public function getBookmarks(): Collection
    {
        return $this->bookmarks;
    }

    public function addBookmark(Bookmark $bookmark): static
    {
        if (!$this->bookmarks->contains($bookmark)) {
            $this->bookmarks->add($bookmark);
            $bookmark->setJob($this);
        }

        return $this;
    }

    public function removeBookmark(Bookmark $bookmark): static
    {
        if ($this->bookmarks->removeElement($bookmark)) {
            // set the owning side to null (unless already changed)
            if ($bookmark->getJob() === $this) {
                $bookmark->setJob(null);
            }
        }

        return $this;
    }
}
