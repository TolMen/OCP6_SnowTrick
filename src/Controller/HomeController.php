<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $trickRepository): Response
    {
        // Récupération de tous les tricks avec leurs images
        $tricks = $trickRepository->findAllWithImages();

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'controller_name' => 'HomeController',
        ]);
    }
}
