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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class EtudiantAdminNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('blocEcheanciers', CollectionType::class, [
                'entry_type' => BlocEcheancierType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true,
            ])
            /*  */;

        $builder->add('valider', SubmitType::class, ['label' => 'ENVOYER A LA VALIDATION', 'attr' => ['class' => 'btn btn-danger btn-ajax']]);
        $builder->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => ['class' => 'btn btn-main btn-ajax']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
            'doc_required' => true,
            'doc_options' => [],
            'validation_groups' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
        $resolver->setRequired(['validation_groups']);
    }
}
