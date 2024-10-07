<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\TrickEditType;
use App\Repository\TrickRepository;
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



    #[Route('/trick/{slug}', name: 'trick_show', requirements: ['slug' => '(?!new)[a-zA-Z0-9\-]+'])]
    public function showTrick(string $slug, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findOneBy(['slug' => $slug]); // Trouver le trick par son slug

        if (!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé !');
        }

        $videosWithEmbedCode = [];
        foreach ($trick->getVideos() as $video) {
            $videosWithEmbedCode[] = $this->getEmbedCode($video->getEmbedCode());
        }
        
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'videosWithEmbedCode'=> $videosWithEmbedCode,
        ]);
    }

    private function getEmbedCode(string $url): string
    {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|(?:www\.)?youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&\n]{11})/', $url, $matches);
        
        if (isset($matches[1])) {
            $videoId = $matches[1];
            // Retourner le code d'intégration
            return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
        }
        
        return ''; // Retourner une chaîne vide si l'URL n'est pas valide
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

            $trick->generateSlug($slugger);

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
            $this->addFlash('success', 'Le trick a été supprimé avec succès !');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide. Impossible de supprimer le trick !');
        }

        return $this->redirectToRoute('app_home');
    }






    
    #[Route('/trick/edit/{id}', name: 'trick_edit')]
    #[IsGranted('ROLE_USER')]
    public function editTrick(Request $request, Trick $trick, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TrickEditType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier le nom du trick
            $existingTrick = $entityManager->getRepository(Trick::class)->findOneBy(['name' => $trick->getName()]);
            if ($existingTrick && $existingTrick->getId() !== $trick->getId()) {
                $form->addError(new FormError('Ce nom de trick existe déjà.'));
                return $this->render('trick/edit.html.twig', [
                    'form' => $form->createView(),
                    'trick' => $trick,
                ]);
            }


            // Récupérer les URLs des vidéos soumises depuis le formulaire
            $submittedVideoUrls = $form->get('videos')->getData(); // URLs soumises

            // Liste des vidéos existantes (dans la base de données)
            $existingVideos = $trick->getVideos()->toArray(); // Convertir la collection en tableau

            // **Étape 1 : Traiter les vidéos existantes**
            foreach ($existingVideos as $existingVideo) {
                $existingUrl = $existingVideo->getEmbedCode();
                if (in_array($existingUrl, $submittedVideoUrls)) {
                    // Si l'URL existe toujours dans le formulaire, on ne la supprime pas
                    $submittedVideoUrls = array_diff($submittedVideoUrls, [$existingUrl]); // Supprimer l'URL de la liste des vidéos soumises
                } else {
                    // Si l'URL n'existe plus dans le formulaire, on la supprime
                    $entityManager->remove($existingVideo);
                }
            }

            // **Étape 2 : Ajouter les nouvelles vidéos**
            foreach ($submittedVideoUrls as $newVideoUrl) {
                // Si l'URL soumise n'est pas déjà dans la base de données, on l'ajoute
                $video = new Videos();
                $video->setEmbedCode(trim($newVideoUrl));
                $video->setDateAdd(new \DateTime());
                $video->setIdTrick($trick); // Associer la vidéo au Trick
                $entityManager->persist($video); // Persister la nouvelle vidéo
            }

            // **Étape 3 : Gérer l'image (si une nouvelle image est uploadée)**
            $imageFile = $form->get('image')->getData(); // Récupérer le fichier d'image
            if ($imageFile instanceof UploadedFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le bon dossier
                $imageFile->move($this->getParameter('images_directory'), $newFilename);

                // Créer ou mettre à jour l'image
                $image = new Images();
                $image->setImgURL('uploads/images/imgFigure/' . $newFilename);
                $image->setDateCreated(new \DateTime());
                $image->setIdTrick($trick);

                $entityManager->persist($image);
            }

            $trick->generateSlug($slugger); 

            // Mettre à jour la date de modification
            $trick->setDateUpdated(new \DateTime());

            // Sauvegarder toutes les modifications (le Trick, les vidéos, et l'image)
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Le trick a été mis à jour avec succès !');

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

        $this->addFlash('success', 'Le trick a été mis à jour avec succès !');

        return $this->redirectToRoute('app_home');
    }
}