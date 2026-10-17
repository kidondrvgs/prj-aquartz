<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\PrestationType;
use App\Repository\ServiceRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_ADMIN')]
class AdminPrestationController extends AbstractController
{
    #[Route('/admin/prestations', name: 'app_admin_prestations', methods: ['GET', 'POST'])]
    #[Route('/admin/prestations/{id}/modifier', name: 'app_admin_prestation_modifier', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function prestations(Request $request, ServiceRepository $serviceRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger, ?Service $prestation = null): Response
    {
        if ($request->attributes->has('id') && $prestation === null) {
            throw $this->createNotFoundException('Prestation introuvable.');
        }
        $nouvelle = $prestation === null;
        if ($nouvelle) {
            $prestation = new Service();
        }
        $images = [];
        foreach (glob($this->getParameter('kernel.project_dir').'/public/images/*.jpg') as $image) {
            $nom = basename($image);
            $images[$nom] = $nom;
        }
        $imageActuelle = $prestation->getImages()[0] ?? $prestation->getSlug().'.jpg';
        $form = $this->createForm(PrestationType::class, $prestation, [
            'images' => $images,
            'image_actuelle' => in_array($imageActuelle, $images, true) ? $imageActuelle : null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $prestation->setImages([$image ?? 'hero-terrassement.jpg']);
            if ($nouvelle) {
                $prestation->setSlug($slugger->slug($prestation->getTitle())->lower()->toString());
            }
            $entityManager->persist($prestation);
            $entityManager->flush();
            $this->addFlash('success', 'La prestation a bien été enregistrée.');

            return $this->redirectToRoute('app_admin_prestations');
        }

        return $this->render('admin/prestations.html.twig', [
            'prestations' => $serviceRepository->findBy([], ['id' => 'ASC']),
            'prestation' => $prestation,
            'form' => $form,
            'nouvelle' => $nouvelle,
        ]);
    }

    #[Route('/admin/prestations/{id}/supprimer', name: 'app_admin_prestation_supprimer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprimer(Request $request, Service $prestation, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('supprimer_prestation_'.$prestation->getId(), $request->request->getString('_token'))) {
            throw $this->createAccessDeniedException('Le formulaire a expiré.');
        }

        // On conserve les prestations déjà utilisées dans des demandes ou des projets
        if (!$prestation->getFkCustomerRequest()->isEmpty() || !$prestation->getProjects()->isEmpty()) {
            $this->addFlash('error', 'Cette prestation est utilisée dans une demande ou un projet et ne peut pas être supprimée.');
        } else {
            try {
                $entityManager->remove($prestation);
                $entityManager->flush();
                $this->addFlash('success', 'La prestation a été supprimée.');
            } catch (ForeignKeyConstraintViolationException) {
                $this->addFlash('error', 'La prestation vient d’être utilisée et ne peut plus être supprimée.');
            }
        }

        return $this->redirectToRoute('app_admin_prestations');
    }
}
