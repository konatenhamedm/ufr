<?php

namespace App\Form;

use App\Entity\LigneDeliberation;
use App\Entity\MatiereExamen;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LigneDeliberationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', TextType::class, ['attr' => ['class' => 'input-money input-note', 'data-max' => 20]])
            ->add('coefficient', IntegerType::class, ['attr' => ['readonly' => 'readonly', 'class' => 'input-coeff']])
           
            ->add('matiereExamen', EntityType::class, [
                'class' => MatiereExamen::class,
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => function (MatiereExamen $matiereExamen) {
                    return $matiereExamen?->getMatiere()->getLibelle();
                },
               
                'attr' => ['class' => 'has-select2']
            ])
        ;

        $builder->get('note')->addModelTransformer(new ThousandNumberTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneDeliberation::class,
        ]);
    }
}
