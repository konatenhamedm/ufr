<?php

namespace App\Form;

use App\Entity\Controle;
use App\Entity\Etudiant;
use App\Entity\Note;
use App\Entity\ValeurNote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'valeurNotes',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => ValeurNoteType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add('numeroEtape', IntegerType::class, [
                'label' => false,
                'mapped' => false
            ])
            ->add('moyenneMatiere', NumberType::class, [])
            ->add('rang', NumberType::class, [
                'empty_data' => '0'
            ])
            ->add('exposant', TextType::class, [])
            /*    ->add('controle', EntityType::class, [
                'class' => Controle::class,
'choice_label' => 'id',
            ]) */
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'getNomComplet',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
