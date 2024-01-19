<?php

namespace App\Form;

use App\DTO\GroupeModuleDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('module')
            ->add('name')
            ->add('child')
            ->add('roles'
                , ChoiceType::class, [
                    'attr' => [
                        'class' => 'select2',
                        'data-choices' => json_encode($options['list_roles'])
                    ]
                    , 'multiple' => true
                    , 'required' => false
                    , 'expanded' => false
                    , 'choices' => $options['roles']
                ]
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupeModuleDTO::class,
            'list_roles' => []
        ]);

        $resolver->setRequired('roles');
        $resolver->setRequired('list_roles');
    }
}
