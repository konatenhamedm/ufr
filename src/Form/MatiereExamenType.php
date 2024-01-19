<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\MatiereExamen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coefficient', IntegerType::class)
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_attr' => function ( Matiere $matiere ) {
                    return ['data-label' => $matiere?->getLibelle(), 'data-field' => '.label'];
                },
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => false,
                'attr' => ['class' => 'has-select2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatiereExamen::class,
        ]);
    }
}
