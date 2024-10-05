<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\TrickEditType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    #[Route('/trick', name: 'app_trick')]
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    #[Route('/trick/new', name: 'trick_new')]
    #[IsGranted('ROLE_USER')]
    public function newTrick(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'existence du nom dans la base de données
            $existingTrick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $trick->getName()]);
            if ($existingTrick) {
                // Si le trick existe déjà, ajoutez un message d'erreur et restez sur le formulaire
                $form->addError(new FormError('Ce nom de trick existe déjà.'));
                return $this->render('trick/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Gérer le fichier image
            $imageFile = $form->get('image')->getData(); // Récupérer le fichier d'image

            if ($imageFile instanceof UploadedFile) {
                // Générer un nom de fichier unique
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le bon dossier
                $imageFile->move($this->getParameter('images_directory'), $newFilename);

                // Créer une nouvelle instance d'Images
                $image = new Images();
                $image->setImgURL('uploads/images/imgFigure/' . $newFilename);
                $image->setDateCreated(new \DateTime());
                $image->setIdTrick($trick);

                // Persister l'image
                $entityManager->persist($image);
            } else {
                $this->addFlash('danger', 'Vous devez ajouter une image.');
                return $this->redirectToRoute('trick_new');
            }

            // Assigner l'utilisateur connecté au trick
            $trick->setUser($this->getUser());
            $trick->setDateCreated(new \DateTime());

            // Enregistrer le trick
            $entityManager->persist($trick);
            $entityManager->flush();

            $embedCode = $form->get('embedCode')->getData();

            if ($embedCode) {
                $video = new Videos();
                $video->setEmbedCode($embedCode);
                $video->setDateAdd(new \DateTime());
                $video->setIdTrick($trick);

                $entityManager->persist($video);
                $entityManager->flush();
            }

            $this->addFlash('success', 'Le trick a été ajouté avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView(),
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
    public function editTrick(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire pour modifier le trick
        $form = $this->createForm(TrickEditType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'existence du nom dans la base de données
            $existingTrick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $trick->getName()]);

            if ($existingTrick && $existingTrick->getId() !== $trick->getId()) {
                $form->addError(new FormError('Ce nom de trick existe déjà.'));
                return $this->render('trick/edit.html.twig', [
                    'form' => $form->createView(),
                    'trick' => $trick,
                ]);
            }

            // Mise à jour des champs
            $entityManager->flush();

            $this->addFlash('success', 'Le trick a été mis à jour avec succès.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
        ]);
    }


    #[Route('/trick/update/{id}', name: 'trick_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateTrick(Request $request, Trick $trick, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les données du formulaire
        $trickName = $request->request->get('name');
        $trickContent = $request->request->get('content');
        $trickCategory = $request->request->get('category');

        // Mise à jour des champs
        $trick->setName($trickName);
        $trick->setContent($trickContent);
        $trick->setCategory($trickCategory);
        $trick->setDateUpdated(new \DateTime());

        // Sauvegarde des changements
        $entityManager->persist($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Le trick a été mis à jour avec succès.');

        return $this->redirectToRoute('app_home');
    }
}
