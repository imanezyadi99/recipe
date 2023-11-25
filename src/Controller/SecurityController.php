<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login',methods:['GET','POST'])]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
     
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'=>$authenticationUtils->getLastAuthenticationError()
        ]);
    }

       #[Route('/deconnexion', 'security.logout')]
       public function logout()
       {

       }


       #[Route('/inscription', name: 'security.registration',methods:['GET','POST'])]
        public function registration(Request $request,PersistenceManagerRegistry $doctrine): Response
        {
        $user=new User();
        $user->setRoles(['ROLE_USER']);
        $form=$this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
         $em = $doctrine->getManager();
         $em->persist($user);
         $em->flush();
         $this->addFlash('success',
                           'Utilisateur a été crée avec succès !'
              );
         return $this->redirectToRoute('security.login');
        }
        return $this->render('security/registration.html.twig',[
            'form'=> $form->createView()
        ]);
       }
    }

