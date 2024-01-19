<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\Niveau;
use App\Entity\NiveauEtudiant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NiveauEtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $type = $options['etat'];



        if ($type == "rejeter") {
            $builder->add('motif', TextareaType::class, []);
        } elseif ($type == 'autre') {


            $builder

                ->add('etudiant', EntityType::class, [
                    'class' => Etudiant::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'getNomComplet',
                    'label' => 'Etudiant',
                    'attr' => ['class' => 'has-select2']
                ])
                ->add('niveau', EntityType::class, [
                    'class' => Niveau::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'getNom',
                    'label' => 'Niveau',
                    'attr' => ['class' => 'has-select2']
                ])
                ->add('filiere', EntityType::class, [
                    'class' => Filiere::class,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Filière',
                    'attr' => ['class' => 'has-select2']
                ]);
        } else {
            $builder->add('datePaiement', DateType::class,  [
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text', 'format' => 'dd/MM/yyyy',
                'label' => 'Date paiement', 'empty_data' => date('d/m/Y'), 'required' => false, 'html5' => false

            ])
                /*->add('montantPreinscription',NumberType::class)*/;
        }

        $builder->add('annuler', SubmitType::class, ['label' => 'Annuler', 'attr' => ['class' => 'btn btn-primary btn-sm', 'data-bs-dismiss' => 'modal']])
            ->add('save', SubmitType::class, ['label' => 'Enregister', 'attr' => ['class' => 'btn btn-main btn-ajax btn-sm']])
            ->add('passer', SubmitType::class, ['label' => 'Valider préinscription', 'attr' => ['class' => 'btn btn-success btn-ajax btn-sm']])
            ->add('rejeter', SubmitType::class, ['label' => 'Réjeter la demande', 'attr' => ['class' => 'btn btn-danger btn-ajax btn-sm']])
            ->add('payer', SubmitType::class, ['label' => 'Payer', 'attr' => ['class' => 'btn btn-warning btn-ajax btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NiveauEtudiant::class,
        ]);
        $resolver->setRequired('etat');
    }
}
