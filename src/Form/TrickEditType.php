<?php

namespace App\Form;

use App\Entity\Trick;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajout de FileType pour l'image
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrickEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('videos', CollectionType::class, [
                'mapped' => false,
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => 'URL de la vidéo',
                    'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez l\'URL de la vidéo ici...']
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'data' => $options['data']->getVideos()->map(fn($video) => $video->getEmbedCode())->toArray(),
                'label' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrez le nom du trick'],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['placeholder' => 'Décrivez le trick ici...', 'rows' => 5],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
                ],
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
                'attr' => ['placeholder' => 'Entrez la catégorie'],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
                ],
            ])
            ->add('image', FileType::class, [ // Ajout du champ image
                'label' => 'Image',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Téléchargez l\'image du trick',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}