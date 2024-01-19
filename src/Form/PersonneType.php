<?php

namespace App\Form;

use App\Entity\Civilite;
use App\Entity\Employe;
use App\Entity\Fonction;
use App\Entity\Genre;
use App\Entity\Personne;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ['label' => 'Nom'])
            ->add('prenom', null, ['label' => 'Prénoms'])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date de naissance',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('lieuNaissance', null, ['label' => 'Lieu de naissance', 'required' => false, 'empty_data' => ''])
            ->add('email', EmailType::class, ['label' => 'Adresse E-mail', 'required' => false, 'empty_data' => ''])
            ->add('contact', null, ['label' => 'Contacts', 'required' => false, 'empty_data' => ''])
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
            ->add('fonction', EntityType::class, [
                'class' => Fonction::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Fonction',
                'attr' => ['class' => 'has-select2']
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
