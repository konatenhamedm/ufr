<?php

namespace App\Form;

use App\Entity\Niveau;
use App\Entity\Preinscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreinscriptionValidationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateValidation', DateType::class, [
                'required' => true,
                'mapped' => true,
                'widget' => 'single_text',
                'label'   => 'Date',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Valider' => 'Valider',
                    'Recaler' => 'Recaler',
                    'Attente_info' => 'En attente informations',
                ],
                'label' => 'Décision',
                'attr' => ['class' => 'has-select2']
            ]);
        $builder->add('commentaire', TextareaType::class, ['label' => 'Commentaire', 'required' => false, 'empty_data' => '']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Preinscription::class,
            // 'validate' => false,
            /*   'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->getEtat() == 'rejet') {
                    return ['Default', 'rejet-preinscription'];
                }
                return ['Default'];
            } */
        ]);
    }
}
