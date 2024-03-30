<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Niveau;
use App\Entity\TypeDocument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*  ->add('description')*/
            /* ->add('libelle') */

            ->add(
                'libelle',
                ChoiceType::class,
                [
                    'placeholder' => 'Choisir un type',
                    'label' => 'Type document',
                    'required'     => true,
                    'expanded'     => false,
                    'attr' => ['class' => 'has-select2'],
                    'multiple' => false,
                    'choices'  => array_flip([
                        'doc_motivation' => 'Lettre de motivation',
                        'doc_cv' => 'Cv',
                        'doc_recommandation' => 'Lettre de recommandation',
                        'doc_piece_identite' => 'pièce d’identité',
                        'doc_extrait_naissance' => 'extrait de naissance ',
                        'doc_photo' => 'Photo',
                    ]),
                ]
            )


            ->add(
                'fichier',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Document',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true
                ]
            )
            /* ->add(
                'fichier',
                FichierType::class,
                [
                    'label' => 'TELECHARGEZ LE DOCUMENT',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true,
                    'validation_groups' => $options['validation_groups'],
                ]
            ) */
            /* ->add('personne')*/
            /*       ->add('typeDocument' ,EntityType::class, [
        'class' => TypeDocument::class,
        'mapped' => true,
        'required' => false,
        'placeholder' => '----',
        'label_attr' => ['class' => 'label-required'],
        'choice_label' => 'libelle',
        'label' => 'Type document',
        'attr' => ['class' => 'has-select2']
    ])*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,

            'doc_required' => true,
            'allow_extra_fields' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
