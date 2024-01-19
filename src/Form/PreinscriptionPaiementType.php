<?php

namespace App\Form;

use App\Entity\NaturePaiement;
use App\Entity\Niveau;
use App\Entity\Preinscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreinscriptionPaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('datePaiement', DateType::class, [
                'required' => true,
                'mapped' => false,
                'widget' => 'single_text',
                'label'   => 'Date de paiement',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('modePaiement', EntityType::class, [
               'class' => NaturePaiement::class,
                'required' => true,
                'mapped' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Mode paiement',
                'attr' => ['class' => 'has-select2']
            ])  ;
                $builder
                    //->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-primary btn-sm', 'data-bs-dismiss' => 'modal']])
                   /* ->add('save', SubmitType::class, ['label' => 'Enregister', 'attr' => ['class' => 'btn btn-main btn-ajax btn-sm']])*/
                  /*  ->add('passer', SubmitType::class, ['label' => 'Valider préinscription', 'attr' => ['class' => 'btn btn-success btn-ajax btn-sm']])*/
                   /* ->add('rejeter', SubmitType::class, ['label' => 'Réjeter la demande', 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])*/
                    ->add('payer', SubmitType::class, ['label' => 'valider', 'attr' => ['class' => 'btn btn-warning btn-ajax btn-sm']])
                    ->add('confirmation', SubmitType::class, ['label' => 'Confirmer', 'attr' => ['class' => 'btn btn-warning btn-ajax btn-sm']]);



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preinscription::class,

        ]);

        $resolver->setRequired('etat');
    }
}
