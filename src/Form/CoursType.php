<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Cours;
use App\Entity\Employe;
use App\Entity\Matiere;
use App\Entity\Niveau;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* ->add('niveau', EntityType::class, [
                'class' => Niveau::class,
                'choice_label' => 'libelle',
            ]) */
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'libelle',
                'attr' => ['class' => 'has-select2 form-select']
            ])
            ->add('anneeScolaire', EntityType::class, [
                'class' => AnneeScolaire::class,
                'choice_label' => 'libelle',
            ])
            /*   ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => 'getNomComplet',
                'attr' => ['class' => 'has-select2']
            ]); */

            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->innerJoin('c.fonction', 'f')
                        ->andWhere('f.code =:code')
                        ->setParameter('code', 'ENS')
                        ->orderBy('c.id', 'ASC');
                },
                'choice_label' => 'nomComplet',
                'label' => 'Reponsable de niveau',
                'attr' => ['class' => 'has-select2 form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
