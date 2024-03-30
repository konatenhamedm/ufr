<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(
                'fichier',
                FichierType::class,
                [
                    'label' => 'Fichier',
                    'label' => 'Logo admin',
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
            'doc_required' => true,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
