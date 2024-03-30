<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Filiere;
use App\Entity\Frais;
use App\Entity\Niveau;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NiveauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, ['label' => 'Code'])
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('filiere', EntityType::class, [
                'class' => Filiere::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Filière',
                'attr' => ['class' => 'has-select2']
            ])
            ->add(
                'frais',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => FraisType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add(
                'infoNiveaux',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => InfoNiveauType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            )
            ->add('responsable', EntityType::class, [
                'class' => Employe::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.fonction', 'f')
                        ->andWhere('f.code =:code')
                        ->setParameter('code', 'DR')
                        ->orderBy('c.id', 'ASC');
                },
                'choice_label' => 'nomComplet',
                'label' => 'Reponsable de niveau',
                'attr' => ['class' => 'has-select2']
            ])
            /*  ->add(
                'cours',
                CollectionType::class,
                [
                    'label'         => false,
                    'entry_type'    => CoursType::class,
                    //'label'         => false,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,

                    'entry_options' => ['label' => false],
                ]
            ) */;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Niveau::class,
        ]);
    }
}
