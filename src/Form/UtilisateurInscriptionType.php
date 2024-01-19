<?php

namespace App\Form;

use App\Entity\Groupe;
use App\Entity\Employe;
use App\Entity\Personne;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UtilisateurInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom',
                'mapped'=>false,
                //'placeholder'=>'Entrez votre nom '
            ])  ->add('email', EmailType::class, ['label' => 'Email',
                'mapped'=>false,
                // 'placeholder'=>'Entrez votre email '
            ])
            ->add('prenoms', TextType::class, ['label' => 'Prénoms',
                'mapped'=>false,
                // 'placeholder'=>'Entrez votre prénoms '
            ])

            ->add('username', TextType::class, ['label' => 'Username',
                'mapped'=>true,
                // 'placeholder'=>'Entrez votre prénoms '
            ])
            ->add('dateNaissance',  DateType::class,  [
                'mapped' => false,
                //'placeholder'=>"Entrez votre date de naissance s'il vous plaît",
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   'format' => 'yyyy-MM-dd',
                'label' => 'Date de naissance', 'empty_data' => date('d/m/Y'), 'required' => false
            ])
            ->add('username', TextType::class, ['label' => 'Pseudo'])

            ->add('plainPassword', RepeatedType::class, 
                [
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'passwordRequired' => false
        ]);

        $resolver->setRequired('passwordRequired');
    }
}
