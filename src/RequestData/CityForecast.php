<?php

declare(strict_types=1);

namespace App\RequestData;

use App\Entity\City;
use Symfony\Component\Validator\Constraints as Assert;

class CityForecast
{
    private ?City $city = null;

    /**
     * @Assert\NotBlank(message="Days field is required")
     * @Assert\Range(
     *     min="1",
     *     minMessage="Min days is 1",
     *     max="3",
     *     maxMessage="Max days is 3"
     * )
     */
    private int $days;

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function setDays(int $days): self
    {
        $this->days = $days;

        return $this;
    }
}
