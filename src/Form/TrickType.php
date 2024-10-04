<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => 'Image (jpg, png, gif)',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ est obligatoire.']),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpg, png, gif).',
                    ])
                ],
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

