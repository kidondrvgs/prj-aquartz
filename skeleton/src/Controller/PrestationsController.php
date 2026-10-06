<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrestationsController extends AbstractController
{
    #[Route('/prestations', name: 'app_prestations')]
    public function index(): Response
    {
        return $this->render('prestations/prestation.html.twig');
    }
}