<?php

namespace App\Form;

use App\Entity\Civilite;
use App\Entity\Etudiant;
use App\Entity\Genre;
use App\Entity\Pays;
use App\Entity\Personne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['type'];
        if( $type == "info"){
            $builder
                ->add('nom', TextType::class, ['label' => 'Nom'])
                ->add('prenom', TextType::class, ['label' => 'Prénoms'])
                ->add('dateNaissance', DateType::class, [
                    // 'widget' => 'single_text',
                    'label'   => 'Date de naissance',
                    'format'  => 'dd/MM/yyyy',
                    'html5' => false,
                    'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   /*'format' => 'yyyy-MM-dd',*/
                ])
                ->add('lieuNaissance', null, ['label' => 'Lieu de naissance', 'required' => false, 'empty_data' => ''])
                ->add('email', EmailType::class, ['label' => 'Adresse E-mail', 'required' => false, 'empty_data' => ''])
                ->add('contact', null, ['label' => 'Téléphone', 'required' => false, 'empty_data' => ''])
                ->add('genre', EntityType::class, [
                    'class' => Genre::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Sexe',
                    'attr' => ['class' => 'has-select2']
                ])
                ->add('civilite', EntityType::class, [
                    'class' => Civilite::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Civilité',
                    'attr' => ['class' => 'has-select2']
                ])


                ->add('ville', TextType::class, ['label' => 'Ville'])
                ->add('adresse', TextType::class, ['label' => 'Adresse géographique permanente'])
                ->add('pays', EntityType::class, [
                    'class' => Pays::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Pays',
                    'attr' => ['class' => 'has-select2']
                ])
                ->add('boite', TextType::class, ['label' => 'Boîte postale'])
               /* ->add('fax', TextType::class, ['label' => 'Fax'])*/
                ->add('employeur', CheckboxType::class, ['label' => 'Employeur', 'required' => false])
                ->add('parent', CheckboxType::class, ['label' => 'Vos parents', 'required' => false])
                ->add('bailleur', CheckboxType::class, ['label' => 'Bailleur de fonds', 'required' => false])
                ->add('vousMeme', CheckboxType::class, ['label' => 'Vous-même', 'required' => false])
                ->add('autre', null, ['label' => false,
                   //'placeholder'=>"Saisissez vos informations personnelles"
                ])
                ->add('radio', CheckboxType::class, ['label' => 'Radio', 'required' => false])
                ->add('presse', CheckboxType::class, ['label' => 'Presse écrite', 'required' => false])
                ->add('affiche', CheckboxType::class, ['label' => 'Affiche', 'required' => false])
                ->add('ministere', CheckboxType::class, ['label' => 'Ministere', 'required' => false])
                ->add('mailing', CheckboxType::class, ['label' => 'Mailing', 'required' => false])
                ->add('siteWeb', CheckboxType::class, ['label' => 'Site web', 'required' => false])
                ->add('autreExistence', TextType::class, ['label' => false])
                ->add('professeur', CheckboxType::class, ['label' => 'Professeur', 'required' => false])
                ->add('amiCollegue', CheckboxType::class, ['label' => 'Ami et collegue', 'required' => false])


                ->add('nom', TextType::class, ['label' => 'Nom'])
                ->add('prenom', TextType::class, ['label' => 'Prénoms'])
                ->add('dateNaissance', DateType::class, [
                    // 'widget' => 'single_text',
                    'label'   => 'Date de naissance',
                    'format'  => 'dd/MM/yyyy',
                    'html5' => false,
                    'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   /*'format' => 'yyyy-MM-dd',*/
                ])
                ->add('lieuNaissance', null, ['label' => 'Lieu de naissance', 'required' => false, 'empty_data' => ''])
                ->add('email', EmailType::class, ['label' => 'Adresse E-mail', 'required' => false, 'empty_data' => ''])
                ->add('contact', null, ['label' => 'Téléphone', 'required' => false, 'empty_data' => ''])
                ->add('genre', EntityType::class, [
                    'class' => Genre::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Sexe',
                    'attr' => ['class' => 'has-select2']
                ])
                ->add('civilite', EntityType::class, [
                    'class' => Civilite::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Civilité',
                    'attr' => ['class' => 'has-select2']
                ])

                ->add('cursusUniversitaires', CollectionType::class, [
                    'entry_type' => CursusUniversitaireType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
                    'allow_add' => true,
                    'label' => false,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'prototype' => true,
                ])
                ->add('cursusProfessionnels', CollectionType::class, [
                    'entry_type' => CursusProfessionnelType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
                    'allow_add' => true,
                    'label' => false,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'prototype' => true,
                ])
           ->add('stages', CollectionType::class, [
                    'entry_type' => StageType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
                    'allow_add' => true,
                    'label' => false,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'prototype' => true,
                ])
                   /*  */

            ;
        }elseif($type == "document"){
            $builder->add('documents', CollectionType::class, [
                'entry_type' => DocumentType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ])  ;
        }else{
            $builder

                ->add('nom', TextType::class, ['label' => 'Nom'])
                ->add('prenom', TextType::class, ['label' => 'Prénoms'])
                ->add('dateNaissance', DateType::class, [
                    // 'widget' => 'single_text',
                    'label'   => 'Date de naissance',
                    'format'  => 'dd/MM/yyyy',
                    'html5' => false,
                    'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   /*'format' => 'yyyy-MM-dd',*/
                ])
                ->add('lieuNaissance', null, ['label' => 'Lieu de naissance', 'required' => false, 'empty_data' => ''])
                ->add('email', EmailType::class, ['label' => 'Adresse E-mail', 'required' => false, 'empty_data' => ''])
                ->add('contact', null, ['label' => 'Téléphone', 'required' => false, 'empty_data' => ''])
                ->add('genre', EntityType::class, [
                    'class' => Genre::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Sexe',
                    'attr' => ['class' => 'has-select2']
                ])
                ->add('civilite', EntityType::class, [
                    'class' => Civilite::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Civilité',
                    'attr' => ['class' => 'has-select2']
                ])

            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
        $resolver->setRequired(['type']);
    }
}
