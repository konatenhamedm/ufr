<?php

namespace App\Form;

use App\Entity\CursusUniversitaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class CursusUniversitaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etablissement', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ etablissement")
                )
            ])
            ->add('annee', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ année")
                )
            ])
            ->add('numeroDiplome', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le numero du diplome")
                )
            ])
            ->add('numeroMatricule', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ numero matricule")
                )
            ])
            ->add('ville', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ ville")
                )
            ])
            ->add('pays', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ pays")
                )
            ])
            ->add('diplome', TextType::class, [
                'label' => 'Diplôme et série',
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ diplome")
                )
            ])
            ->add('mention', TextType::class, [
                'required' => false,
                'label' => 'Mention(facultatif)'
            ])

            ->add(
                'bac',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Diplome',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true
                ]
            )

            ->add(
                'releve',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Relevé notes',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true
                ]
            )
            /*->add('personne')*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CursusUniversitaire::class,
            'doc_required' => true,
            'allow_extra_fields' => true
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
