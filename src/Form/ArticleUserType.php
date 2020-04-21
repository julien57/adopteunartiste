<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Contenu de l\'article',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez ajouter du contenu à votre article']),
                    new Length(['min' => 50, 'minMessage' => 'Le contenu de votre article est trop court'])
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez ajouter un titre'])
                ]
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo d\'illustration',
                'data_class' => null,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez ajouter une photo d\'illustration'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
