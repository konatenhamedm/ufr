<?php

namespace App\Form;

use App\Entity\Matiere;
use App\Entity\MatiereUe;
use App\Entity\UniteEnseignement;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereUeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('coef', TextType::class, [
                'attr' => ['class' => 'input-money input-note', 'data-max' => 20]
            ])
            /*    ->add('visible') */
            ->add('nombreCredit', TextType::class, ['attr' => ['class' => 'input-money credit', 'data-max' => 20]])
            ->add('noteEliminatoire', TextType::class, ['attr' => ['class' => 'input-money input-note', 'data-max' => 20]])
            ->add('moyenneValidation', TextType::class, ['attr' => ['class' => 'input-money input-note', 'data-max' => 20]])

            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'libelle',
                'required' => false,
                'attr' => ['class' => 'matiere has-select2 form-select'],
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                },
            ]);
        $builder->get('nombreCredit')->addModelTransformer(new ThousandNumberTransformer());
        $builder->get('coef')->addModelTransformer(new ThousandNumberTransformer());
        $builder->get('noteEliminatoire')->addModelTransformer(new ThousandNumberTransformer());
        $builder->get('moyenneValidation')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatiereUe::class,
        ]);
    }
}
