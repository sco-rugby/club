<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController {

    #[Route('/', name: 'app_index')]
    public function index(): Response {
        return $this->render('index.html.twig');
    }

    #[Route("/dashboard", name: "dashboard_module", methods: ['GET'])]
    public function dashboard(): Response {
        return $this->render('dashboard.html.twig');
    }

    #[Route("/config", name: "config_module", methods: ['GET'])]
    public function import(): Response {
        return $this->render('config.html.twig');
    }

}
