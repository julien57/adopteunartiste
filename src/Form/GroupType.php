<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cover', FileType::class, [
                'label' => ' ',
                'data_class' => null
            ])
            ->add('avatar', FileType::class, [
                'label' => ' ',
                'data_class' => null
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du groupe'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du groupe *'
            ])
            ->add('email', TextType::class, [
                'label' => 'Email public',
                'required' => false
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
            'data_class' => Group::class,
        ]);
    }
}
