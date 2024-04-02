<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Inscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionAffectationClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'required' => false,
                'placeholder' => '----',
                'query_builder' => function ($er) use ($options) {
                    return $er->createQueryBuilder('a')->andWhere('a.niveau = :niveau')
                        ->setParameter('niveau', $options['niveau']);
                },
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Classe',
                'attr' => ['class' => 'has-select2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
        $resolver->setRequired('niveau');
    }
}
