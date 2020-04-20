<?php

namespace App\Form;

use App\Entity\DomainArtist;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfosUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse mail',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('birthAt', BirthdayType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'label' => 'Date de naissance',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('domainArtist', EntityType::class, [
                'class' => DomainArtist::class,
                'choice_label' => 'title',
                'label' => 'Domaine artistique',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false
            ])
            ->add('language', LanguageType::class, [
                'label' => 'Langue',
                'required' => false
            ])
            ->add('avatar', FileType::class, [
                'label' => 'MModifier photo de profil',
                'required' => false,
                'data_class' => null
            ])
            ->add('cover', FileType::class, [
                'label' => 'Modifier photo de couverture',
                'required' => false,
                'data_class' => null
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site Web',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
