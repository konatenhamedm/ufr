<?php

namespace App\Form;

use App\Entity\Stage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('employeur')
            ->add('contact')
            ->add('dateDebut',  DateType::class,  [
                'mapped' => true,
                //'placeholder'=>"Entrez votre date de naissance s'il vous plaît",
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   'format' => 'yyyy-MM-dd',
                'label' => 'Date debut', 'empty_data' => date('d/m/Y'), 'required' => false
            ])
            ->add('dateFin',  DateType::class,  [
                'mapped' => true,
                //'placeholder'=>"Entrez votre date de naissance s'il vous plaît",
                'attr' => ['class' => 'datepicker no-auto skip-init'], 'widget' => 'single_text',   'format' => 'yyyy-MM-dd',
                'label' => 'Date de fin', 'empty_data' => date('d/m/Y'), 'required' => false
            ])
            ->add('activite')
            /*->add('personne')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stage::class,
        ]);
    }
}
