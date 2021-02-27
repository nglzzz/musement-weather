<?php

declare(strict_types=1);

namespace App\Controller\Api\V3;

use App\Controller\BaseController;
use App\Entity\City;
use App\Handler\Api\City\CityGetterHandler;
use App\Handler\Api\City\CityListHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v3/cities", name="api_ve_cities_")
 */
class CityController extends BaseController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function getList(CityListHandler $handler): Response
    {
        try {
            $result = $handler->handle();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return $this->emptyResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($result);
    }

    /**
     * @Route("/{city}", name="get", methods={"GET"})
     */
    public function getCity(City $city, CityGetterHandler $handler): Response
    {
        try {
            $result = $handler->handle($city);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return $this->emptyResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($result);
    }
}
