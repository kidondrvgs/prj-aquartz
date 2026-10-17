<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrestationsController extends AbstractController
{
    // Page prestations - affiche toutes les prestations de la BDD
    #[Route('/prestations', name: 'app_prestations')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        // On récupère toutes les prestations
        $prestations = $serviceRepository->findAll();

        return $this->render('prestations/prestation.html.twig', [
            'prestations' => $prestations,
        ]);
    }
}
