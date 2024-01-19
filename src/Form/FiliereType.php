<?php

namespace App\Form;

use App\Entity\Filiere;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiliereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, ['label' => 'Code'])
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'has-editor']
            ])
            ->add('montantPreinscription', TextType::class, [
                'label' => 'Montant Préinscription', 
                'attr' => ['class' => 'input-money']
            ])
        ;

        $builder->get('montantPreinscription')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filiere::class,
        ]);
    }
}
