<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]


class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?array $images = null;

    /**
     * @var Collection<int, CustomerRequest>
     */
    #[ORM\ManyToMany(targetEntity: CustomerRequest::class, inversedBy: 'services')]
    private Collection $fk_customer_request;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'FkService')]
    private Collection $projects;

    public function __construct()
    {
        $this->fk_customer_request = new ArrayCollection();
        $this->projects = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return Collection<int, CustomerRequest>
     */
    public function getFkCustomerRequest(): Collection
    {
        return $this->fk_customer_request;
    }

    public function addFkCustomerRequest(CustomerRequest $fkCustomerRequest): static
    {
        if (!$this->fk_customer_request->contains($fkCustomerRequest)) {
            $this->fk_customer_request->add($fkCustomerRequest);
        }

        return $this;
    }

    public function removeFkCustomerRequest(CustomerRequest $fkCustomerRequest): static
    {
        $this->fk_customer_request->removeElement($fkCustomerRequest);

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setFkService($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getFkService() === $this) {
                $project->setFkService(null);
            }
        }

        return $this;
    }
}
