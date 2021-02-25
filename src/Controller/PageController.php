<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends BaseController
{
    /**
     * If we remove this action then symfony will show "Symfony welcome page"
     * In this case every person can know that we use PHP and symfony.
     *
     * @Route("/", name="index_request", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return new Response('', Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/ping", name="ping_request", methods={"GET"})
     */
    public function pingAction(): Response
    {
        return new Response('OK', Response::HTTP_OK);
    }
}
