<?php

namespace App\Form;

use App\DTO\GroupeDTO;
use App\Entity\Groupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('description', null, ['label' => 'Description', 'required' => false, 'empty_data' => ''])
            ->add('modules', CollectionType::class, [
                'entry_type' => GroupeModuleType::class,
                'entry_options' => ['label' => false, 'roles' => $options['roles']],
                'label'         => false,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupeDTO::class,
            'old_roles' => []
        ]);

        $resolver->setRequired('roles');
        $resolver->setRequired('old_roles');
    }
}
