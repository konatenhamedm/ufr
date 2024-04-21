<?php

namespace App\Form;

use App\Entity\BlocEcheancier;
use App\Entity\EcheancierProvisoire;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcheancierProvisoireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TypeIntegerType::class, [])
            /*   ->add('dateVersement') */
            ->add('dateVersement', DateType::class, [
                'widget' => 'single_text',
                'label'   => false,
                // 'format'  => 'dd/MM/yyyy',
                'html5' => true,
                //  'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('montant', TextType::class, ['attr' => ['class' => 'input-money input-mnt']])
            /*  ->add('blocEcheancier', EntityType::class, [
                'class' => BlocEcheancier::class,
                'choice_label' => 'id',
            ]) */;
        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EcheancierProvisoire::class,
        ]);
    }
}
