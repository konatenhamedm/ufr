<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Employe;
use App\Entity\Filiere;
use App\Entity\Frais;
use App\Entity\Niveau;
use App\Repository\ClasseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NiveauAddEnseignantWithClasseType extends AbstractType
{
    private $em;
    private $classRepo;

    public function __construct(EntityManagerInterface $em, ClasseRepository $classRepo)
    {
        $this->classRepo = $classRepo;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['type'];


        if ($type == "new") {
            $builder
                ->add('classe', EntityType::class, [
                    'class' => Classe::class,
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Classe',
                    'attr' => ['class' => 'has-select2 '],
                ]);
        } else {
            $builder
                ->add('classe', EntityType::class, [
                    'class' => Classe::class,
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => '----',
                    'label_attr' => ['class' => 'label-required'],
                    'choice_label' => 'libelle',
                    'label' => 'Classe',
                    'attr' => ['class' => 'has-select2 '],
                    'data' => $this->em->getReference(
                        Classe::class,
                        $this->classRepo->find($options['type'])->getId()
                    ),
                ]);
        }

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
        $resolver->setRequired(['type']);
    }
}
