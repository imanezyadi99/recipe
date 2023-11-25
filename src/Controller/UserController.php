<?php

namespace App\Controller;
use App\Form\UserType;
use App\Entity\User;
use App\Form\UserPasswordType;
use App\Repository\UserRepository; // Import the User repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === choosenUser ")]
    #[Route('/utilisateur/edition/{id}', name: 'user.edit')]
    public function edit($id, Request $request, PersistenceManagerRegistry $doctrine,UserPasswordHasherInterface $hasher): Response
    {
     $entityManager = $doctrine->getManager();
     $choosenUser = $entityManager->getRepository(User::class)->find($id);

        $form=$this->createForm(UserType::class,$choosenUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($choosenUser,$form->getData()->getPlainPassword()))
            $choosenUser = $form->getData();
            $em->persist($choosenUser);
            $em->flush();
            $this->addFlash('success',
                           'Les informations de votre compte ont bien été modifiés !'
                 );
            return $this->redirectToRoute('recipe');

            } else {

            $this->addFlash('warning',
                           'Le mode passe renseigné est incorrect !'
                            );
                }
        
        return $this->render('user/edit.html.twig', [
            'form'=> $form->createView()       
         ]);
       }

    #[Security("is_granted('ROLE_USER') and user === choosenUser ")]
    #[Route('/utilisateur/edition_nouveau_modepasse/{id}', name: 'user.edit.password')]
    public function editPassword( $id, Request $request, PersistenceManagerRegistry $doctrine,UserPasswordHasherInterface $hasher, UserRepository $userRepository): Response
    {   
        $entityManager = $doctrine->getManager();
        $choosenUser = $entityManager->getRepository(User::class)->find($id);
        $form= $this->createForm(UserPasswordType::class,$choosenUser);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if ($hasher->isPasswordValid($choosenUser, $form->get('plainPassword')->getData())) {

            $choosenUser->setPassword(
           $hasher->hashPassword(
            $choosenUser,
            $form->get('newPassword')->getData()
           )

          );
            $this->addFlash('success',
                           'Le mot de passe a été modifiée'
              );

              $entityManager->persist($choosenUser);
              $entityManager->flush();
            return $this->redirectToRoute('recipe');

            } else {

            $this->addFlash('warning',
                           'Le mode passe renseigné est incorrect !'
                            );
                }
            }
        return $this->render('user/edit_password.html.twig', [
            'form'=> $form->createView()       
         ]);
       }
    }

