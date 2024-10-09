<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $trick = $options['data'];

        $builder
            ->add('video1', TextType::class, [
                'mapped' => false,
                'label' => 'Vidéo 1 (URL)',
                'required' => false,
                'data' => $trick->getVideos()->get(0) ? $trick->getVideos()->get(0)->getEmbedCode() : '', // Première vidéo ou vide
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez l\'URL de la première vidéo']
            ])
            ->add('video2', TextType::class, [
                'mapped' => false,
                'label' => 'Vidéo 2 (URL)',
                'required' => false,
                'data' => $trick->getVideos()->get(1) ? $trick->getVideos()->get(1)->getEmbedCode() : '', // Deuxième vidéo ou vide
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez l\'URL de la deuxième vidéo']
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new NotBlank(['message' => 'Ce champ est obligatoire.'])],
                'attr' => ['placeholder' => 'Entrez le nom du trick']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [new NotBlank(['message' => 'Ce champ est obligatoire.'])],
                'attr' => ['placeholder' => 'Décrivez le trick ici...', 'rows' => 5]
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'constraints' => [new NotBlank(['message' => 'Ce champ est obligatoire.'])],
                'attr' => ['placeholder' => 'Entrez la catégorie']
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
