<?php

namespace App\Form;

use App\Entity\UniteFormation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniteFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, ['label' => 'Libellé'])
            ->add('sigle', null, ['label' => 'Sigle'])
            ->add('email', null, ['label' => 'Adresse E-mail', 'required' => false, 'empty_data' => ''])
            ->add('contact', null, ['label' => 'Contact', 'required' => false, 'empty_data' => ''])
            ->add('siteWeb', UrlType::class, ['label' => 'Site Web',  'required' => false, 'empty_data' => ''])
            ->add('boitePostale', null, ['label' => 'Boite Postale',  'required' => false, 'empty_data' => ''])
            ->add('logo', FichierType::class, ['label' => 'Logo', 'doc_options' => $options['doc_options'], 'required' => $options['doc_options']['doc_required'] ?? true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UniteFormation::class,
        ]);
        $resolver->setRequired('doc_options');
    }
}
