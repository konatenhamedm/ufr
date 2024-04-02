<?php

namespace App\Form;

use App\Entity\Controle;
use App\Entity\Etudiant;
use App\Entity\Note;
use App\Entity\ValeurNote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*    ->add(
                'valeurNotes',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => ValeurNote::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            ) */
            ->add('MoyenneMatiere')
            /*    ->add('controle', EntityType::class, [
                'class' => Controle::class,
'choice_label' => 'id',
            ]) */
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
