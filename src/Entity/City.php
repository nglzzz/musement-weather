<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class City
{
    /**
     * @Groups({"Default"})
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Country $country;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="Name is required")
     * @Assert\Length(max=100, maxMessage="Name cannot be longer than 100 characters")
     */
    private string $name;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="string", length=50, unique=true)
     *
     * @Assert\NotBlank(message="Code is required")
     * @Assert\Length(max=100, maxMessage="Code cannot be longer than 100 characters")
     */
    private string $code;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="integer", unique=true)
     *
     * @Assert\NotBlank(message="SourceId is required")
     * @Assert\Range(min="1", minMessage="SourceId must be more than 0")
     */
    private int $sourceId;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank(message="Latitude is required")
     */
    private float $latitude;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank(message="Longitude is required")
     */
    private float $longitude;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Groups({"Default"})
     *
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $updatedAt;

    public function __construct(string $name, string $code, Country $country)
    {
        $this->name = $name;
        $this->code = $code;
        $this->country = $country;

        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSourceId(): int
    {
        return $this->sourceId;
    }

    public function setSourceId(int $sourceId): self
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateLastUpdateDate(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
