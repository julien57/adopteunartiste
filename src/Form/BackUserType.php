<?php

namespace App\Form;

use App\Entity\DomainArtist;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isFront = $options['isFront'];

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
                'label' => ($isFront === true) ? 'Date de naissance (format jj-mm-aaaa)' : 'Date de naissance',
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
        ;

        if ($isFront && $isFront === true) {
            $builder
                ->add('password',RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les champs doivent être identiques.',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Répétez mot de passe'],
                    'empty_data' => ''
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isFront' => 'isFront'
        ]);
    }
}
