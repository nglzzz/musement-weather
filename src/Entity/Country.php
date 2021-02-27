<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 * @ORM\Table(name="countries")
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

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
     * @ORM\Column(type="string", length=5, unique=true)
     *
     * @Assert\NotBlank(message="Code is required")
     * @Assert\Length(max=5, maxMessage="IsoCode cannot be longer than 5 characters")
     */
    private string $isoCode;

    public function __construct(string $name, string $isoCode)
    {
        $this->name = $name;
        $this->isoCode = $isoCode;
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

    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = $isoCode;

        return $this;
    }
}
