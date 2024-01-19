<?php

namespace App\Form;

use App\Entity\Deliberation;
use App\Entity\Etudiant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliberationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateExamen', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ]) 
            ->add('infoPreinscription', DeliberationPreinscriptionType::class, ['examen' => $options['examen']])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Validé' => 'valide',
                    'Rejeté' => 'rejet'
                ],
                'placeholder' => '----',
                'label' => 'Décision',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('commentaire',TextareaType::class, ['label' => 'Observations', 'required' => false, 'empty_data' => ''])
            /*->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'nomComplet',
                'label' => 'Etudiant',
                'query_builder' => fn($er) => $er->withoutDeliberation($options['examen']),
                'attr' => ['class' => 'has-select2']
            ])*/
            ->add(
                'ligneDeliberations',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => LigneDeliberationType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,
                    
                    'entry_options' => ['label' => false],
                ]
            )
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deliberation::class,
        ]);
        $resolver->setRequired('examen');
    }
}
