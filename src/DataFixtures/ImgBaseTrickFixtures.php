<?php

namespace App\DataFixtures;

use App\Entity\Images;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ImgBaseTrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Liste des images avec leur URL et l'ID du trick associé
        $imagesData = [
            ['uploads/images/imgFigure/figureBase/ollieTrick.jpg', 1],
            ['uploads/images/imgFigure/figureBase/nosegrabTrick.jpg', 2],
            ['uploads/images/imgFigure/figureBase/tailgrabTrick.jpg', 3],
            ['uploads/images/imgFigure/figureBase/frontside360Trick.jpg', 4],
            ['uploads/images/imgFigure/figureBase/backside360Trick.jpg', 5],
            ['uploads/images/imgFigure/figureBase/frontside540Trick.jpg', 6],
            ['uploads/images/imgFigure/figureBase/backflipTrick.jpg', 7],
            ['uploads/images/imgFigure/figureBase/methodgrabTrick.jpg', 8],
            ['uploads/images/imgFigure/figureBase/shiftyTrick.jpg', 9],
            ['uploads/images/imgFigure/figureBase/cab720Trick.jpg', 10],
        ];

        // Date d'ajout des images : 02/10/2024
        $dateAdd = new \DateTime('2024-10-02');

        // Pour chaque image, on la rattache à un trick existant par son ID
        foreach ($imagesData as [$imgUrl, $trickId]) {
            // Récupérer le trick associé à cet ID
            $trick = $manager->getRepository(Trick::class)->find($trickId);

            if ($trick) {
                // Créer une nouvelle instance de l'image
                $image = new Images();
                $image->setImgURL($imgUrl);
                $image->setIdTrick($trick); // Associer l'image au trick
                $image->setDateCreated($dateAdd); // Ajouter la date d'ajout

                // Persist l'image
                $manager->persist($image);
            } else {
                // Gérer le cas où le trick avec cet ID n'existe pas
                echo "Le Trick avec l'ID $trickId n'existe pas.\n";
            }
        }

        // Sauvegarder toutes les nouvelles images en base de données
        $manager->flush();
    }
}
