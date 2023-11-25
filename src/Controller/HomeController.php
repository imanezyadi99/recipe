<?php

namespace App\Controller;
use App\Entity\Ingredient;
use App\Entity\User;
use App\Form\CrudType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\IngredientRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class HomeController extends AbstractController
{
   
    #[Route('/ingredient', name: 'main')]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository ,PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
        $repository->findBy(['user'=>$this->getUser()]),
        $request->query->getInt('page', 1),
        25
    );

        return $this->render('home/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/create', name: 'createe')]
    #[IsGranted('ROLE_USER')]
    public function create( Request $request, PersistenceManagerRegistry $doctrine)
        {
        $crud=new Ingredient();
        $form=$this->createForm(CrudType::class,$crud);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
               
           $crud=$form->getData();
            $crud->setUser($this->getUser());
            $em = $doctrine->getManager();
            $em->persist($crud);
            $em->flush();
            $this->addFlash('success',
                           'Votre ingrédient a été crée avec succès !'
                 );
            return $this->redirectToRoute('main');
        }
        return $this->render('home/create.html.twig',[
            'form'=> $form->createView()
     ]);
    }

     #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
     #[Route('/updatee/{id}', name: 'updatee')]
     public function update( Request $request,$id, PersistenceManagerRegistry $doctrine)
        {
        $crud=$doctrine->getRepository(Ingredient::class)->find($id);
        $form=$this->createForm(CrudType::class,$crud);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($crud);
            $em->flush();
            $this->addFlash('notice','success');
            return $this->redirectToRoute('main');
        }
        return $this->render('home/update.html.twig',[
            'form'=> $form->createView()
     ]);
    }


      #[Route('/deletee/{id}', name: 'deletee')]
      public function delete($id, PersistenceManagerRegistry $doctrine)
        {
       $data=$doctrine->getRepository(Ingredient::class)->find($id);
       $em=$doctrine->getManager();
       $em->remove($data);
       $em->flush();
       $this->addFlash('notice',"deleted success");
       return $this->redirectToRoute('main');

       }

}
