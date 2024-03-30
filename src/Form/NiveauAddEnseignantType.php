<?php

namespace App\Form;

use App\Entity\Classe;
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

class NiveauAddEnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
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
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
