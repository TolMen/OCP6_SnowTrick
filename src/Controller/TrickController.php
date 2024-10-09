<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\TrickEditType;
use App\Repository\TrickRepository;
use App\Entity\Commentaire;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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


    /* 
    Affiche la page principale des tricks. 
    C'est la page d'accueil pour cette section du site.
    Elle ne contient actuellement qu'un affichage de base.
    */
    #[Route('/trick', name: 'app_trick')]
    public function index(): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }


    /* 
    Affiche un trick spécifique basé sur son slug. 
    Cette méthode récupère les détails du trick, les vidéos associées, et les commentaires liés à ce trick. 
    Elle permet également aux utilisateurs de laisser un nouveau commentaire, qui est ensuite sauvegardé en base de données. 
    */
    #[Route('/trick/{slug}', name: 'trick_show', requirements: ['slug' => '(?!new)[a-zA-Z0-9\-]+'])]
    public function showTrick(
        string $slug,
        TrickRepository $trickRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $trick = $trickRepository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            throw $this->createNotFoundException('Aucun trick trouvé !');
        }

        // Récupérer les vidéos avec le code d'intégration
        $videosWithEmbedCode = [];
        foreach ($trick->getVideos() as $video) {
            $videosWithEmbedCode[] = $this->getEmbedCode($video->getEmbedCode());
        }

        // Récupérer tous les commentaires
        $commentaires = $entityManager->getRepository(Commentaire::class)
            ->findBy(['id_trick' => $trick], ['dateCreated' => 'DESC']);

        // Création d'un nouveau commentaire
        $commentaire = new Commentaire();
        $form = $this->createFormBuilder($commentaire)
            ->add('message', TextareaType::class, [
                'label' => 'Laissez un commentaire',
                'attr' => ['class' => 'form-control']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setIdUser($this->getUser());
            $commentaire->setIdTrick($trick);
            $commentaire->setDateCreated(new \DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');

            return $this->redirectToRoute('trick_show', ['slug' => $slug]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'videosWithEmbedCode' => $videosWithEmbedCode,
            'commentaires' => $commentaires,
            'form' => $form->createView(),
        ]);
    }


    /* 
    Transforme une URL YouTube en code d'intégration HTML <iframe>. 
    Cette méthode est utilisée pour afficher les vidéos dans la vue d'un trick. 
    */
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


    /*
    Permet la création d'un nouveau trick. 
    Cette fonction traite un formulaire pour créer un trick, uploader une image associée, et sauvegarder le tout en base de données. 
    Elle gère également la génération d'un slug pour le trick.
    */
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


    /*
    Supprime un trick de la base de données ainsi que tous les commentaires qui lui sont associés. 
    La suppression est sécurisée par un jeton CSRF pour prévenir les attaques.
    */
    #[Route('/trick/delete/{id}', name: 'trick_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {

            // Récupérer tous les commentaires associés au trick
            $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['id_trick' => $trick]);

            // Supprimer tous les commentaires
            foreach ($commentaires as $commentaire) {
                $entityManager->remove($commentaire);
            }

            $entityManager->remove($trick);
            $entityManager->flush();
            $this->addFlash('success', 'Le trick a été supprimé avec succès !');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide. Impossible de supprimer le trick !');
        }

        return $this->redirectToRoute('app_home');
    }


    /*
    Supprime une image associée à un trick, à la fois dans le système de fichiers du serveur et dans la base de données. 
    Là aussi, la suppression est protégée par un jeton CSRF.
    */
    #[Route('/image/delete/{id}', name: 'delete_image', methods: ['POST'])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier la validité du token CSRF
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            // Supprimer l'image physiquement du serveur
            $imagePath = $this->getParameter('images_directory') . '/' . basename($image->getImgURL());
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Supprimer l'entité image de la base de données
            $entityManager->remove($image);
            $entityManager->flush();

            // Message flash pour informer l'utilisateur de la suppression
            $this->addFlash('success', 'L\'image a été supprimée avec succès.');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide. Impossible de supprimer l\'image.');
        }

        // Redirection vers la page du trick
        return $this->redirectToRoute('trick_show', ['slug' => $image->getIdTrick()->getSlug()]);
    }


    /*
    Permet la modification d'un trick existant. 
    L'utilisateur peut changer les vidéos et les images du trick, ainsi que ses autres informations. 
    Cette méthode met à jour les entrées correspondantes dans la base de données.
    */
    #[Route('/trick/edit/{id}', name: 'trick_edit')]
    #[IsGranted('ROLE_USER')]
    public function editTrick(Request $request, Trick $trick, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TrickEditType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les deux URL de vidéos
            $video1Url = $form->get('video1')->getData();
            $video2Url = $form->get('video2')->getData();

            // Vérifier si les vidéos ont été supprimées via les champs cachés
            if ($request->request->get('remove_video_1') === '1') {
                $video1Url = null; // La vidéo 1 doit être supprimée
            }

            if ($request->request->get('remove_video_2') === '1') {
                $video2Url = null; // La vidéo 2 doit être supprimée
            }

            // Supprimer les vidéos existantes
            foreach ($trick->getVideos() as $video) {
                $entityManager->remove($video);
            }

            // Ajouter ou mettre à jour les vidéos
            if (!empty($video1Url)) {
                $video1 = new Videos();
                $video1->setEmbedCode($video1Url);
                $video1->setDateAdd(new \DateTime());
                $video1->setIdTrick($trick);
                $entityManager->persist($video1);
            }

            if (!empty($video2Url)) {
                $video2 = new Videos();
                $video2->setEmbedCode($video2Url);
                $video2->setDateAdd(new \DateTime());
                $video2->setIdTrick($trick);
                $entityManager->persist($video2);
            }

            // Gestion de l'image 1
            $imageFile1 = $form->get('image1')->getData();
            if ($imageFile1 instanceof UploadedFile) {
                $originalFilename = pathinfo($imageFile1->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile1->guessExtension();

                // Déplacer le fichier dans le bon dossier
                $imageFile1->move($this->getParameter('images_directory'), $newFilename);

                // Créer ou mettre à jour l'image 1
                $image1 = new Images();
                $image1->setImgURL('uploads/images/imgFigure/' . $newFilename);
                $image1->setDateCreated(new \DateTime());
                $image1->setIdTrick($trick);

                $entityManager->persist($image1);
            }

            // Gestion de l'image 2 (même logique)
            $imageFile2 = $form->get('image2')->getData();
            if ($imageFile2 instanceof UploadedFile) {
                $originalFilename = pathinfo($imageFile2->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile2->guessExtension();

                $imageFile2->move($this->getParameter('images_directory'), $newFilename);

                $image2 = new Images();
                $image2->setImgURL('uploads/images/imgFigure/' . $newFilename);
                $image2->setDateCreated(new \DateTime());
                $image2->setIdTrick($trick);

                $entityManager->persist($image2);
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


    /*
    Cette méthode met à jour les informations de base d'un trick (nom, contenu, catégorie) et sauvegarde les modifications. 
    Elle est appelée via une requête POST.
    */
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
