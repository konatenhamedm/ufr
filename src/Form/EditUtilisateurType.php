<?php

namespace App\Form;

use App\Entity\{Utilisateur, Employe, Groupe};
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['label' => 'Ancien Mot de passe', 'mapped' => false])
            ->add('newPassword', RepeatedType::class, 
                [
                    'mapped' => false,
                    'type'            => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'required'        => $options['passwordRequired'],
                    'first_options'   => ['label' => 'Mot de passe'],
                    'second_options'  => ['label' => 'Répétez le mot de passe'],
                    'constraints' => [
                        new NotBlank(['message' => 'Veuillez renseigner un mot de passe', 'groups' => ['Registration']])
                    ]
                ]
            )
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'passwordRequired' => false
        ]);

        $resolver->setRequired('passwordRequired');
    }
}
