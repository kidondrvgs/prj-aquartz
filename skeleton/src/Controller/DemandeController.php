<?php

namespace App\Controller;

use App\Form\CustomerRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeController extends AbstractController
{
    // Page nouvelle demande - affiche le formulaire
    #[Route('/nouvelle-demande', name: 'app_nouvelle_demande')]
    public function nouvelleDemande(Request $request): Response
    {
        // On crée le formulaire
        $form = $this->createForm(CustomerRequestType::class);

        // On récupère ce que le client a envoyé
        $form->handleRequest($request);

        // Formulaire envoyé et tous les champs sont bons
        if ($form->isSubmitted() && $form->isValid()) {
            // L'enregistrement en BDD sera ajouté plus tard
            $this->addFlash('success', 'Votre demande a bien été envoyée. Nous vous répondons sous 48 h.');

            // On recharge la page pour ne pas renvoyer le formulaire avec F5
            return $this->redirectToRoute('app_nouvelle_demande');
        }

        // On affiche la page avec le formulaire
        return $this->render('demande/nouvelle_demande.html.twig', [
            'form' => $form,
        ]);
    }
}
