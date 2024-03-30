<?php

namespace App\Form;

use App\Entity\CursusProfessionnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class CursusProfessionnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emploi', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ emploi")
                )
            ])
            ->add('employeur', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ employeur")
                )
            ])
            ->add('contact', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ contact")
                )
            ])
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
            ->add('activite', TextType::class, [
                "constraints" => array(
                    new NotNull(null, "S'il vous veillez renseigner le champ activité")
                )
            ])
            /*->add('personne')*/;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CursusProfessionnel::class,
        ]);
    }
}
