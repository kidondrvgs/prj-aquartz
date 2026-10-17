<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class EspaceController extends AbstractController
{
    #[Route('/mon-espace', name: 'app_espace', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute($this->isGranted('ROLE_ADMIN') ? 'app_admin' : 'app_mes_demandes');
    }
}
