<?php

namespace App\Form;

use App\Entity\FraisInscription;
use App\Entity\NaturePaiement;
use App\Entity\Versement;
use App\Form\DataTransformer\ThousandNumberTransformer;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VersementType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateVersement', DateType::class, [
                'widget' => 'single_text',
                'label'   => 'Date de versement',
                'format'  => 'dd/MM/yyyy',
                'html5' => false,
                'attr'    => ['autocomplete' => 'off', 'class' => 'datepicker no-auto'],
            ])
            ->add('montant', TextType::class, ['label' => 'Montant', 'attr' => ['class' => 'input-money']])
            ->add('nature', EntityType::class, [
                'class' => NaturePaiement::class,
                'required' => false,
                'placeholder' => '----',
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Nature paiement',
                'attr' => ['class' => 'has-select2']
            ])
            ->add('fraisInscription', EntityType::class, [
                'class' => FraisInscription::class,
                'required' => false,
                'placeholder' => '----',
                'query_builder' => function ($er) use ($options) {
                    return $er->createQueryBuilder('a')->andWhere('a.inscription = :inscription')
                        ->setParameter('inscription', $options['inscription']);
                },
                'choice_attr' => function (FraisInscription $fraisInscription) {
                    return ['data-solde' => $fraisInscription?->getSolde()];
                },
                'label_attr' => ['class' => 'label-required'],
                'choice_label' => 'libelle',
                'label' => 'Type de frais',
                'attr' => ['class' => 'has-select2']
            ])
           
        ;

        $builder->get('montant')->addModelTransformer(new ThousandNumberTransformer());

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            /** @var FormInterface */
            $form = $event->getForm();
           

            if (!empty($data['fraisInscription'])) {
                $fraisInscription = $this->em->getRepository(FraisInscription::class)->find($data['fraisInscription']);
                $montant = intval(str_replace(' ', '', $data['montant']));
                $solde = $fraisInscription->getSolde();

                if ($montant > $solde) {
                    $form->addError(new FormError('Le montant du versement ne doit pas exécder '.Utils::formatNumber($solde)));
                }
            }
            
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Versement::class,
        ]);
        $resolver->setRequired('inscription');
    }
}
