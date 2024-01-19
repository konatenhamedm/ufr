<?php

namespace App\Form;

use App\Entity\Mention;
use App\Form\DataTransformer\ThousandNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MentionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', null, ['label' => 'Code'])
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('moyenneMin', TextType::class, ['attr' => ['class' => 'input-money']])
            ->add('moyenneMax', TextType::class, ['attr' => ['class' => 'input-money']])
        ;

        $fields = ['moyenneMin', 'moyenneMax'];
        foreach ($fields as $field) {
            $builder->get($field)->addModelTransformer(new ThousandNumberTransformer(2));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mention::class,
        ]);
    }
}
