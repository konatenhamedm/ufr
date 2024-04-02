<?php

namespace App\Form;

use App\Entity\Controle;
use App\Entity\Cours;
use App\Entity\Semestre;
use App\Entity\Session;
use App\Entity\TypeControle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ControleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('max')
            /*  ->add('dateSaisie')
            ->add('dateCompo') */
            /*  ->add('cour', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'id',
            ]) */
            ->add('type', EntityType::class, [
                'class' => TypeControle::class,
                'choice_label' => 'id',
            ])
            ->add('session', EntityType::class, [
                'class' => Session::class,
                'choice_label' => 'id',
            ])
            ->add('semestre', EntityType::class, [
                'class' => Semestre::class,
                'choice_label' => 'id',
            ])
            ->add(
                'notes',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => NoteType::class,
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
            'data_class' => Controle::class,
        ]);
    }
}
