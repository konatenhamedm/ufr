<?php

namespace App\Form;

use App\Entity\BlocEcheancier;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlocEcheancierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('dateInscription', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date inscription',
                // 'format'  => 'dd/MM/yyyy',
                'html5' => true,
                //  'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('total', TextType::class, ['attr' => ['class' => 'input-money input-mnt total']])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'attr' => [
                    'class' => 'classe'
                ],
                'choice_label' => 'libelle',
            ])
            ->add('echeancierProvisoires', CollectionType::class, [
                'entry_type' => EcheancierProvisoireType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ]);
        $builder->get('total')->addModelTransformer(new ThousandNumberTransformer());
        /*  ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'id',
            ]) */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlocEcheancier::class,
        ]);
    }
}
