<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected function emptyResponse(int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return new Response('', $status, $headers);
    }

    protected function jsonFormErrorResponse(FormInterface $form): Response
    {
        return $this->json([
            'errors' => $this->getFormErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function getFormErrors(FormInterface $form): array
    {
        $formErrors = $form->getErrors(true, true);

        $errors = [];

        foreach ($formErrors as $error) {
            /** @var FormInterface $field */
            $field = $error->getOrigin();

            $errors[$field->getName()] = $error->getMessage();
        }

        return $errors;
    }
}
