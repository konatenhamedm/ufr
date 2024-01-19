<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\TypeMatiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null,['label' => 'Code'])
            ->add('ordre', IntegerType::class, ['label' => 'Numéro Ordre'])
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('typeMatiere', EntityType::class, [
                'class' => TypeMatiere::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Type de matière',
                'attr' => ['class' => 'has-select2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
