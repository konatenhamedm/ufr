<?php

namespace App\Form;

use App\Entity\Echeancier;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcheancierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCreation', DateType::class, [
                'label' => 'Date paiement',
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto skip-init'],
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'widget' => 'single_text',

            ])
            ->add('montant', TextType::class, [
                'label' => 'Montant',
                'attr' => ['class' => 'input-money montant-input']
            ])
            /* ->add('inscription') */;
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Echeancier::class,
        ]);
    }
}
