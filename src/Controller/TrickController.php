<?php

namespace App\Controller;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrickController extends AbstractController
{
    #[Route('/trick', name: 'app_trick')]
    public function index(): Response
    {
        // Vous pouvez récupérer les tricks de la base de données ici
        // Assurez-vous d'importer votre repository TrickRepository

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            // 'tricks' => $tricks // Passez la liste des tricks ici
        ]);
    }

    #[Route('/trick/delete/{id}', name: 'trick_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $entityManager->remove($trick);
            $entityManager->flush();
            $this->addFlash('success', 'Le trick a été supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide. Impossible de supprimer le trick.');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/trick/edit/{id}', name: 'trick_edit')]
    #[IsGranted('ROLE_USER')]
    public function editTrick(Trick $trick): Response
    {
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/trick/update/{id}', name: 'trick_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateTrick(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        $trickName = $request->request->get('name');

        $trick->setName($trickName);

        $entityManager->persist($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Le trick a été mis à jour avec succès.');

        return $this->redirectToRoute('app_home');
    }
}
