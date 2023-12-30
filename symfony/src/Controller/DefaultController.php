<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController {
    #[Route('/', name: 'home')]
    #[Route('/{path}', requirements: ['path' => '.+'])]
    #[Route('/hello-world', name: 'hello-world')]
    function index(): Response {
        return new Response('Hello world');
    }
}