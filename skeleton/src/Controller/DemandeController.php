<?php

namespace App\Controller;

use App\Entity\CustomerRequest;
use App\Form\CustomerRequestType;
use App\Repository\CustomerRequestRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Toutes les pages des demandes sont réservées aux clients connectés
#[IsGranted('ROLE_USER')]
class DemandeController extends AbstractController
{
    // Page nouvelle demande - affiche le formulaire et enregistre la demande
    #[Route('/nouvelle-demande', name: 'app_nouvelle_demande')]
    public function nouvelleDemande(Request $request, EntityManagerInterface $entityManager, ServiceRepository $serviceRepository): Response
    {
        // Nouvelle demande vide, le formulaire va la remplir
        $demande = new CustomerRequest();

        // On crée le formulaire relié à la demande
        $form = $this->createForm(CustomerRequestType::class, $demande);

        // Prestation déjà choisie si on vient de la page prestations
        $idPrestation = $request->query->get('prestation');
        if ($idPrestation) {
            $form->get('prestation')->setData($serviceRepository->find($idPrestation));
        }

        // On récupère ce que le client a envoyé
        $form->handleRequest($request);

        // Formulaire envoyé et tous les champs sont bons
        if ($form->isSubmitted() && $form->isValid()) {
            // Le client connecté
            $user = $this->getUser();

            // On relie la demande au compte du client
            $demande->setUser($user);

            // Nom, prénom et email repris du compte
            $demande->setFirstName($user->getFirstName());
            $demande->setLastName($user->getLastName());
            $demande->setEmail($user->getEmail());

            // Une nouvelle demande a toujours le statut "Nouvelle"
            $demande->setStatus('Nouvelle');

            // La prestation choisie dans la liste
            $prestation = $form->get('prestation')->getData();
            $demande->addService($prestation);

            // On enregistre en BDD
            $entityManager->persist($demande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande a bien été envoyée. Nous vous répondons sous 48 h.');

            // On recharge la page pour ne pas renvoyer le formulaire avec F5
            return $this->redirectToRoute('app_nouvelle_demande');
        }

        // On affiche la page avec le formulaire
        return $this->render('demande/nouvelle_demande.html.twig', [
            'form' => $form,
        ]);
    }

    // Page mes demandes - liste des demandes du client connecté
    #[Route('/mes-demandes', name: 'app_mes_demandes')]
    public function mesDemandes(Request $request, CustomerRequestRepository $customerRequestRepository): Response
    {
        // Statut choisi dans les filtres
        $statut = $request->query->get('statut');

        // On cherche seulement les demandes du client connecté
        $criteres = ['user' => $this->getUser()];

        // Si un filtre est choisi, on l'ajoute
        if ($statut) {
            $criteres['status'] = $statut;
        }

        // La plus récente en premier
        $demandes = $customerRequestRepository->findBy($criteres, ['id' => 'DESC']);

        // Nombre total de demandes du client, pour le filtre "Toutes"
        $total = $customerRequestRepository->count(['user' => $this->getUser()]);

        return $this->render('demande/mes_demandes.html.twig', [
            'demandes' => $demandes,
            'statut' => $statut,
            'total' => $total,
        ]);
    }

    // Page détail d'une demande
    // Symfony va chercher tout seul la demande qui a l'id de l'URL
    #[Route('/mes-demandes/{id}', name: 'app_detail_demande')]
    public function detailDemande(CustomerRequest $demande): Response
    {
        // Un client ne peut voir que ses propres demandes
        if ($demande->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('demande/detail_demande.html.twig', [
            'demande' => $demande,
        ]);
    }
}
