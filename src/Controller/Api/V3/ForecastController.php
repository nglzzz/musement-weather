<?php

declare(strict_types=1);

namespace App\Controller\Api\V3;

use App\Controller\Api\ApiBaseController;
use App\Form\ForecastType;
use App\Handler\Api\Forecast\ForecastHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v3/forecast", name="api_v3_forecast_")
 */
class ForecastController extends ApiBaseController
{
    /**
     * @Route("/", name="forecast", methods={"GET"})
     */
    public function forecastAction(Request $request, ForecastHandler $handler): Response
    {
        $form = $this->createForm(ForecastType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forecastRequestData = $form->getData();

            $result = $handler->handle($forecastRequestData);

            return $this->json($result);
        }

        return $this->jsonFormErrorResponse($form);
    }
}
