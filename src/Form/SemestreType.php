<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Semestre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SemestreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actif', CheckboxType::class, [
                'label' => 'actif',
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
            ->add('coef', NumberType::class)
            ->add(
                'bloque',
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
                    'label' => false,
                ]
            )
            ->add('anneeScolaire', EntityType::class, [
                'class' => AnneeScolaire::class,
                'choice_label' => 'libelle',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Semestre::class,
        ]);
    }
}
