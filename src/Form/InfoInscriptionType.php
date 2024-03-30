<?php

namespace App\Form;

use App\Entity\InfoInscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoInscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCredit', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date au crédit',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Confirmer' => 'valide',
                    'Rejeter' => 'rejete',
                    'En attente traitement' => 'attente_traitement',
                ],
                'mapped' => false,
                'placeholder' => '----',
                'label' => 'Décision',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('observation', TextareaType::class, ['label' => 'Observations', 'required' => false, 'empty_data' => '']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoInscription::class,
        ]);
    }
}
