<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\City;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CityService
{
    private NormalizerInterface $serializer;

    public function __construct(NormalizerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function normalizeCity(City $city, array $groups = ['Default']): array
    {
        return $this->serializer->normalize($city, null, [
            'groups' => $groups,
            'format' => 'long',
        ]);
    }
}
