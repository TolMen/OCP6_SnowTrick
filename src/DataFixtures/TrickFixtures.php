<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User; // Importer l'entité User
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Récupérer l'utilisateur existant avec l'ID 15
        $user = $manager->getRepository(User::class)->find(15);
        
        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw new \Exception('Aucun utilisateur avec l\'ID 15 n\'existe. Assurez-vous que l\'utilisateur est créé avant de charger les tricks.');
        }

        $tricks = [
            ['Ollie', 'Un saut réalisé sans utiliser de kicker, en frappant le tail de la planche contre le sol pour prendre de la hauteur.', 'Sauts'],
            ['Nose Grab', 'Attraper le nez de la planche pendant un saut.', 'Grabs'],
            ['Tail Grab', 'Attraper le tail de la planche pendant un saut.', 'Grabs'],
            ['Frontside 360', 'Un saut où le snowboarder effectue une rotation complète de 360° dans le sens frontside.', 'Rotations'],
            ['Backside 360', 'Un saut avec une rotation de 360° dans le sens backside.', 'Rotations'],
            ['Frontside 540', 'Un saut avec une rotation de 540°, avec l’épaule avant du snowboarder qui mène le mouvement.', 'Rotations'],
            ['Backflip', 'Un saut périlleux arrière où le snowboarder effectue une rotation en arrière.', 'Flips'],
            ['Method Grab', 'Attraper la carre arrière de la planche avec la main avant tout en fléchissant le corps vers le haut.', 'Grabs'],
            ['Shifty', 'Pendant un saut, le snowboarder fait pivoter son corps et sa planche dans des directions opposées avant de ramener la planche dans sa position normale.', 'Tweaks'],
            ['Cab 720', 'Une rotation de 720° effectuée en switch (position inversée) dans le sens frontside.', 'Rotations']
        ];

        foreach ($tricks as [$name, $content, $category]) {
            $trick = new Trick();
            $trick->setName($name);
            $trick->setContent($content);
            $trick->setCategory($category);

            // Date de création fixée au 23 septembre 2024
            $trick->setDateCreated(new \DateTime('2024-09-23'));
            $trick->setDateUpdated(null);

            // Associer l'utilisateur au trick
            $trick->setUser($user); // Lier l'utilisateur

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
