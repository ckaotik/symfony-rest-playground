<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {
    protected const CURRENT_API_VERSION = 'v1';

    #[Route('/', name: 'home')]
    public function index(Request $request): Response {
        return $this->render('page/home.html.twig', [
            'hostname' => $request->headers->get('host'),
        ]);
    }

    /**
     * Redirect unversioned API calls to current version.
     */
    #[Route('/api/{version}', requirements: ['version' => '(?!v\d+/).+'])]
    public function currentApi(Request $request, string $version): Response {
        return $this->redirect(
            '/api/' . static::CURRENT_API_VERSION . '/' . $version,
            Response::HTTP_MOVED_PERMANENTLY
        );
    }
}
