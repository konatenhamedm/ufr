<?php

namespace App\Form;

use App\DTO\InscriptionDTO;
use App\Entity\Civilite;
use App\Entity\Genre;
use App\Entity\Niveau;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'mapped' => true,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'getFullLibelle',
                'label' => 'Niveau',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('dateNaissance',  DateType::class,  [
                'mapped' => true,
                //'placeholder'=>"Entrez votre date de naissance s'il vous plaît",
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   'format' => 'yyyy-MM-dd',
                'label' => 'Date de naissance', 'empty_data' => date('d/m/Y'), 'required' => false
            ])
           /* ->add('username', TextType::class, ['label' => 'Nom utilisateur', 'attr' => ['placeholder' => '']])*/

            ->add('email', EmailType::class, [
                'label' => 'Email (Login)', 'attr' => ['placeholder' => '']

            ])
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'] ?? true,
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            )
            ->add('nom', TextType::class, ['label' => 'Nom', 'attr' => ['placeholder' => '']])
            ->add('prenom', TextType::class, ['label' => 'Prénoms', 'attr' => ['placeholder' => '']])
            ->add('contact', null, ['label' => 'Contact(s)', 'attr' => ['placeholder' => ''], 'required' => false, 'empty_data' => ''])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date de naissance',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here

        ]);
    }
}
