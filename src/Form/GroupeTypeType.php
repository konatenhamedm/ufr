<?php

namespace App\Form;

use App\Entity\Controle;
use App\Entity\GroupeType;
use App\Entity\TypeControle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*  ->add('numeroColonne', IntegerType::class, [
                'label' => false,
                'mapped' => false
            ]) */
            ->add('type', EntityType::class, [
                'class' => TypeControle::class,
                'choice_label' => 'code',
                'label'   => false,
                'attr'    => ['class' => 'has-select2 '],
            ])
            ->add('dateNote', DateType::class, [
                'widget' => 'single_text',
                'label'   => false,
                'format'  => 'dd/MM/yyyy',
                'empty_data' => '',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add(
                'coef',
                ChoiceType::class,
                [

                    'label' => false,
                    'required'     => false,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        '20' => '20',
                        '10' => '10',

                    ]),

                ]
            )
            /* ->add('coef') */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupeType::class,
        ]);
    }
}
