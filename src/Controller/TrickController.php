<?php

namespace App\Controller;

use App\Entity\Trick; // Import de l'entité Trick
use Doctrine\ORM\EntityManagerInterface; // Import pour la gestion de la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // Pour vérifier les droits d'accès

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
    #[IsGranted('ROLE_USER')] // Vérifie que l'utilisateur est connecté
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Vérification du jeton CSRF
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            // Suppression du trick
            $entityManager->remove($trick);
            $entityManager->flush();

            // Message flash pour informer l'utilisateur
            $this->addFlash('success', 'Le trick a été supprimé avec succès.');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide. Impossible de supprimer le trick.');
        }

        // Redirection vers la liste des tricks ou la page d'accueil
        return $this->redirectToRoute('app_home'); // Remplacez par votre route de redirection
    }
}
