<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnneeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actif', CheckboxType::class, [
                'label' => 'actif',
                'required' => false,
            ])
            ->add('verrou', CheckboxType::class, [
                'label' => 'Verrouillé',
                'required' => false,
            ])
            ->add('libelle')
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date  début',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date fin',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])

            /*   ->add(
                'actif',
                ChoiceType::class,
                [
                    'choices' => [
                        'Oui' => 'oui',
                        'Non' => 'non',

                    ],
                    // 'choice_value' => null,
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                    'data' => 'non',
                ]
            ) */
            /*  ->add('actif') */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnneeScolaire::class,
        ]);
    }
}
