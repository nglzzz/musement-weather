<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    public function testPingAction(): void
    {
        $client = self::createClient();
        $client->request('GET', '/ping');

        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertEquals('OK', $client->getResponse()->getContent());
    }
}
