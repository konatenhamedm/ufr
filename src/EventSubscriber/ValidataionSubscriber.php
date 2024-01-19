<?php

namespace App\EventSubscriber;

use App\Entity\Inscription;
use App\Entity\NiveauEtudiant;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;


class ValidataionSubscriber implements EventSubscriberInterface
{
  private $em;
  private $sensRepository;
  protected $security;
  protected $magasin;
  protected $repo;
  protected  $service;
  protected  $workflow;

  private function numero($code)
  {

    $query = $this->em->createQueryBuilder();
    $query->select("count(a.id)")
      ->from(NiveauEtudiant::class, 'a');

    $nb = $query->getQuery()->getSingleScalarResult();
    if ($nb == 0) {
      $nb = 1;
    } else {
      $nb = $nb + 1;
    }
    return ($code.'-'.date("y") .'-'. str_pad($nb, 3, '0', STR_PAD_LEFT));
  }


  public function __construct(EntityManagerInterface $em, \Symfony\Component\Workflow\Registry $workflow,Security $security)
  {
    ///  $this->security = $security;
      /// $security
      $this->security =$security;
    $this->em = $em;
    $this->workflow = $workflow;

  }

  public function handleValidation(TransitionEvent $event): void
  {

    $transition_name = $event->getTransition()->getName();
    $entity = $event->getSubject();

    //  dd($entity);
    $entity->setDateValidation(new \DateTime());
    $entity->setMotif("RAS");
    $entity->setCode($this->numero($entity->getNiveau()->getCode()));
    $this->em->flush();


  }

    public function handlePayer(TransitionEvent $event): void
    {

        $transition_name = $event->getTransition()->getName();
        $entity = $event->getSubject();
        $this->em->flush();


        //dd($entity->getPrestataire());
        $inscription= new Inscription();
        $inscription->setNiveauEtudiant($entity);
        $inscription->setDatePaiement($entity->getDatePaiement());
        $inscription->setCodeUtilisateur($this->security->getUser()->getUserIdentifier());
        $inscription->setDateInscription(new \DateTime());
        $inscription->setMontant($entity->getFiliere()->getMontantPreinscription());

        $this->em->persist($inscription);
        $this->em->flush();


    }




  public static function getSubscribedEvents(): array
  {
    return [
      'workflow.preinscription.transition.passer' => 'handleValidation',
      'workflow.preinscription.transition.payer' => 'handlePayer',

    ];
  }
}
