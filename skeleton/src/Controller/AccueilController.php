<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    // Page d'accueil - affiche les 4 premières prestations
    #[Route('/', name: 'app_accueil')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        // findBy(critères, tri, nombre max)
        $prestations = $serviceRepository->findBy([], ['id' => 'ASC'], 4);

        return $this->render('Accueil/accueil.html.twig', [
            'prestations' => $prestations,
        ]);
    }
}
