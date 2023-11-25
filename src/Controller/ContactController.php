<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\MailService;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ContactRepository;




class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(
        Request $request,
        EntityManagerInterface $manager,
        PersistenceManagerRegistry $doctrine,
        MailService $mailService
     ): Response

         {
        $contact= new Contact();
        if($this->getUser()){
            $contact->setFullName($this->getUser()->getFullName())
                    ->setEmail($this->getUser()->getEmail());
             }
        $form=$this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contact=$form->getData();
            $em = $doctrine->getManager();
            $em->persist($contact);
            $em->flush();

           $mailService->sendEmail(
            $contact->getEmail(),
            $contact->getSujet(),
            'emails/contact.html.twig',
            ['contact'=>$contact]
             );

           
            $this->addFlash('success',
                           'Votre demande envoyé avec succès !'
                 );
            return $this->redirectToRoute('contact.index');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
