<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api\V3;

use App\Entity\City;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CityControllerTest extends WebTestCase
{
    public function testGetListWithValidJson(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/v3/cities/');

        $content = $client->getResponse()->getContent();

        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertJson($content);

        $decodedContent = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        foreach ($decodedContent as $item) {
            $this->assertCityKeys($item);
        }
    }

    public function testGetCityReturnsHttpOkWithValidJson(): void
    {
        $client = self::createClient();

        foreach ($this->getAllCityIds($client) as $cityId) {
            $client->request('GET', \sprintf('/api/v3/cities/%d', $cityId));

            $content = $client->getResponse()->getContent();
            self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
            self::assertJson($content);

            $decodedContent = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            $this->assertCityKeys($decodedContent);
        }
    }

    public function testGetCityReturnsNotFoundWithIvalidCityId(): void
    {
        $client = self::createClient();

        $existedCityIds = $this->getAllCityIds($client);
        $nonExistedId = \array_shift($existedCityIds) + 100;

        $client->request('GET', \sprintf('/api/v3/cities/%d', $nonExistedId));

        self::assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    private function getAllCityIds(KernelBrowser $client): array
    {
        $doctrine = $client->getContainer()->get('doctrine');
        $cities = $doctrine->getRepository(City::class)->findAll();

        return \array_map(fn (City $city) => $city->getId(), $cities);
    }

    private function assertCityKeys(array $cityItem): void
    {
        self::assertArrayHasKey('name', $cityItem);
        self::assertArrayHasKey('code', $cityItem);
        self::assertArrayHasKey('latitude', $cityItem);
        self::assertArrayHasKey('longitude', $cityItem);
        self::assertArrayHasKey('createdAt', $cityItem);
        self::assertArrayHasKey('updatedAt', $cityItem);
    }
}
