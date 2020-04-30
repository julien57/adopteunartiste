<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupSocialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('facebook', UrlType::class, [
                'label' => 'Lien Facebook',
                'required' => false
            ])
            ->add('twitter', UrlType::class, [
                'label' => 'Lien Twitter',
                'required' => false
            ])
            ->add('youtube', UrlType::class, [
                'label' => 'Lien Youtube',
                'required' => false
            ])
            ->add('instagram', UrlType::class, [
                'label' => 'Lien Instagram',
                'required' => false
            ])
            ->add('twitch', UrlType::class, [
                'label' => 'Lien Twitch',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
