<?php

namespace App\Form;

use App\Entity\Examen;
use App\Entity\MatiereExamen;
use App\Entity\Niveau;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('code', null, ['label' => 'Code'])
            ->add('dateExamen', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date de début',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'getFullLibelleSigle',
                'label' => 'Niveau',
                'attr' => ['class' => 'has-select2']
            ])
            ->add(
                'matiereExamens',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => MatiereExamenType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Examen::class,
        ]);
    }
}
