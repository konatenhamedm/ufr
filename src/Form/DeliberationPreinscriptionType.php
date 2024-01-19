<?php

namespace App\Form;

use App\Entity\DeliberationPreinscription;
use App\Entity\Preinscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliberationPreinscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('preinscription', EntityType::class, [
                'class' => Preinscription::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'nomComplet',
                'label' => 'Candidat',
                'query_builder' => fn($er) => $er->withoutDeliberation($options['examen']),
                'attr' => ['class' => 'has-select2']
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeliberationPreinscription::class,
        ]);

        $resolver->setRequired('examen');
    }
}
