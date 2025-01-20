<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index
{
    public function index(): Response
    {
        return new JsonResponse([]);
    }
}
