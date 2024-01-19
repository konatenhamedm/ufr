<?php

namespace App\Form;

use App\Entity\InfoPreinscription;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoPreinscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', TextType::class, ['label' => 'Montant à payer', 'attr' => ['class' => 'input-money', 'readonly' => 'readonly']])
           
        ;

        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoPreinscription::class,
        ]);
    }
}
