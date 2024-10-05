<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Image (jpg, png, gif) / (Obligatoire)',
                'mapped' => false, // Ce champ n'est pas mappé à l'entité Trick
                'required' => true, // Une image est obligatoire
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']), // Ajout de la contrainte NotBlank
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpg, png, gif).',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control',
                ],
            ])
            ->add('embedCode', TextType::class, [
                'label' => 'URL Vidéo (Optionnel)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'video/*',
                    'class' => 'form-control',
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom (Obligatoire)',
                'attr' => ['placeholder' => 'Entrez le nom du trick'],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description (Obligatoire)',
                'attr' => ['placeholder' => 'Décrivez le trick ici...', 'rows' => 5],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
                ],
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie (Obligatoire)',
                'attr' => ['placeholder' => 'Entrez la catégorie'],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
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
