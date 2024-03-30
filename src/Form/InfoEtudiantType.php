<?php

namespace App\Form;

use App\Entity\InfoEtudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoEtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add(
                'habiteAvec',
                ChoiceType::class,
                [
                    'choices' => [
                        'Le pere' => 'pere',
                        'La mere' => 'mere',
                        'Les deux' => 'deux',
                        'Un tuteur/Une tutrice' => 'tuteur_tutrice'

                    ],
                    'label' => false,
                    // 'choice_value' => null,
                    'multiple' => false,
                    'expanded' => true,
                    'required' => false,
                    'data' => 'pere'
                ]
            )
            ->add('tuteurNomPrenoms', TextType::class, [
                'label' => 'Nom et  prénoms',
                'required' => false,
            ])
            ->add('tuteurFonction', TextType::class, [
                'label' => 'Fonction',
                'required' => false,
            ])
            ->add('tuteurContact', TextType::class, [
                'label' => 'Contacts',
                'required' => false,
            ])
            ->add('tuteurDomicile', TextType::class, [
                'label' => 'Domicile',
                'required' => false,
            ])
            ->add('tuteurEmail', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('corresNomPrenoms', TextType::class, [
                'label' => 'Nom et  prénoms',
                'required' => false,
            ])
            ->add('corresFonction', TextType::class, [
                'label' => 'Fonction',
                'required' => false,
            ])
            ->add('corresContacts', TextType::class, [
                'label' => 'Contacts',
                'required' => false,
                'attr' => ['placeholder' => 'N° de recption des SMS ']
            ])
            ->add('corresDomicile', TextType::class, [
                'label' => 'Domicile',
                'required' => false,
            ])
            ->add('corresEmail', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('pereNomPrenoms', TextType::class, [
                'label' => 'Nom et  prénoms',
                'required' => false,
            ])
            ->add('pereFonction', TextType::class, [
                'label' => 'Fonction',
                'required' => false,
            ])
            ->add('pereContacts', TextType::class, [
                'label' => 'Contacts',
                'required' => false,
            ])
            ->add('pereDomicile', TextType::class, [
                'label' => 'Domicile',
                'required' => false,
            ])
            ->add('pereEmail', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('mereNomPrenoms', TextType::class, [
                'label' => 'Nom et  prénoms',
                'required' => false,
            ])
            ->add('mereFonction', TextType::class, [
                'label' => 'Fonction',
                'required' => false,
            ])
            ->add('mereContacts', TextType::class, [
                'label' => 'Contacts',
                'required' => false,
            ])
            ->add('mereDomicile', TextType::class, [
                'label' => 'Domicile',
                'required' => false,
            ])
            ->add('mereEmail', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoEtudiant::class,
        ]);
    }
}
