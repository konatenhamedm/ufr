<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Niveau;
use App\Entity\TypeDocument;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          /*  ->add('description')*/
            ->add('libelle')

            ->add('fichier', FichierType::class,
                ['label' => 'TELECHARGEZ LE DOCUMENT',
                  //  'label' => false,
                    'doc_options' => $options['doc_options'],
                    'required' => $options['doc_required'] ?? true])
           /* ->add('personne')*/
     /*       ->add('typeDocument' ,EntityType::class, [
        'class' => TypeDocument::class,
        'mapped' => true,
        'required' => false,
        'placeholder' => '----',
        'label_attr' => ['class' => 'label-required'],
        'choice_label' => 'libelle',
        'label' => 'Type document',
        'attr' => ['class' => 'has-select2']
    ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
            'doc_required' => true,
            'doc_options' => [],
        ]);
        $resolver->setRequired('doc_options');
        $resolver->setRequired('doc_required');
    }
}
