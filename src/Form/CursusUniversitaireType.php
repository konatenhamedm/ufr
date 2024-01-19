<?php

namespace App\Form;

use App\Entity\CursusUniversitaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursusUniversitaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etablissement')
            ->add('annee')
            ->add('ville')
            ->add('pays')
            ->add('diplome')
            ->add('mention')
            /*->add('personne')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CursusUniversitaire::class,
        ]);
    }
}
