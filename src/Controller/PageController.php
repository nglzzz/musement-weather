<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends BaseController
{
    /**
     * @Route("/", name="index_request", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/ping", name="ping_request", methods={"GET"})
     */
    public function pingAction(): Response
    {
        return new Response('OK', Response::HTTP_OK);
    }
}
