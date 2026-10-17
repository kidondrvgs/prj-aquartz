<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CompteController extends AbstractController
{
    #[Route('/supprimer-mon-compte', name: 'app_supprimer_compte', methods: ['GET', 'POST'])]
    public function supprimer(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // On crée le formulaire de confirmation
        $form = $this->createFormBuilder(null, [
            'csrf_protection' => true,
            'csrf_token_id' => 'supprimer_compte',
        ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => ['autocomplete' => 'current-password'],
                'constraints' => [
                    new NotBlank(message: 'Saisissez votre mot de passe.'),
                    new UserPassword(message: 'Le mot de passe est incorrect.'),
                ],
            ])
            ->add('confirmation', CheckboxType::class, [
                'label' => 'Je confirme la suppression définitive de mon compte et de mes demandes.',
                'constraints' => [
                    new IsTrue(message: 'Confirmez la suppression pour continuer.'),
                ],
            ])
            ->getForm();
        // On récupère le formulaire envoyé
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            // On supprime tout ensemble pour éviter une suppression incomplète
            $entityManager->wrapInTransaction(function () use ($entityManager, $user): void {
                foreach ($user->getCustomerRequests() as $demande) {
                    foreach ($demande->getServices()->toArray() as $service) {
                        $demande->removeService($service);
                    }
                    $entityManager->remove($demande);
                }
                $entityManager->remove($user);
            });

            // Le formulaire a déjà vérifié la protection de sécurité
            return $security->logout(false) ?? $this->redirectToRoute('app_accueil');
        }

        return $this->render('compte/supprimer.html.twig', ['form' => $form]);
    }
}
