<?php

namespace App\Controller;

use App\Entity\CustomerRequest;
use App\Form\TraitementDemandeType;
use App\Repository\CustomerRequestRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index(CustomerRequestRepository $demandes, ServiceRepository $prestations): Response
    {
        return $this->render('admin/accueil.html.twig', [
            'total' => $demandes->count([]),
            'nouvelles' => $demandes->count(['status' => 'Nouvelle']),
            'enCours' => $demandes->count(['status' => 'En cours']),
            'prestations' => $prestations->count([]),
            'demandes' => $demandes->findBy([], ['id' => 'DESC'], 5),
        ]);
    }

    #[Route('/admin/demandes', name: 'app_admin_demandes', methods: ['GET'])]
    #[Route('/admin/demandes/{id}', name: 'app_admin_demande', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function demandes(Request $request, CustomerRequestRepository $customerRequestRepository, EntityManagerInterface $entityManager, ?CustomerRequest $demande = null): Response
    {
        if ($request->attributes->has('id') && $demande === null) {
            throw $this->createNotFoundException('Demande introuvable.');
        }
        $statut = $request->query->getString('statut');
        if ($statut !== '' && !in_array($statut, CustomerRequest::STATUTS, true)) {
            throw $this->createNotFoundException('Statut inconnu.');
        }

        // On filtre les demandes si un statut est choisi
        $criteres = [];
        if ($statut !== '') {
            $criteres['status'] = $statut;
        }
        $demandes = $customerRequestRepository->findBy($criteres, ['id' => 'DESC']);

        if ($demande === null && count($demandes) > 0) {
            $demande = $demandes[0];
        }
        $form = null;
        if ($demande) {
            $form = $this->createForm(TraitementDemandeType::class, $demande, [
                'action' => $this->generateUrl('app_admin_demande', ['id' => $demande->getId(), 'statut' => $statut]),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Le statut et la réponse ont été enregistrés. La réponse est visible dans l’espace client.');

                return $this->redirectToRoute('app_admin_demande', ['id' => $demande->getId()]);
            }
        }

        $formulaire = null;
        if ($form) {
            $formulaire = $form->createView();
        }

        return $this->render('admin/demandes.html.twig', [
            'demandes' => $demandes,
            'demande' => $demande,
            'form' => $formulaire,
            'statut' => $statut,
            'statuts' => CustomerRequest::STATUTS,
            'total' => $customerRequestRepository->count([]),
        ]);
    }
}
