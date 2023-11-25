<?php

namespace App\Controller;
use App\Entity\Recipe;
use App\Entity\Mark;
use App\Form\RecipeType;
use App\Form\MarkType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\RecipeRepository;
use App\Repository\MarkRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class RecipeController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'recipe')]
    public function index(RecipeRepository $repository ,PaginatorInterface $paginator, Request $request): Response
    {
        $recettes = $paginator->paginate(
        $repository->findBy(['user'=>$this->getUser()]),
        $request->query->getInt('page', 1),
        25
    );
        return $this->render('recipe/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    
    #[Route('/recette/public', name: 'recipe.index.public')]
     public function indexPublic(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request
        ):Response {
         $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page',1),
            10
        );

        return $this->render('recipe/index_public.html.twig',[
          'recipes' => $recipes
        ]);

        }

    #[Security("is_granted('ROLE_USER') and recipe.getIsPublic() === true")]
    #[Route('/recette/{id}', name: 'recipe.show')]
    public function show(RecipeRepository $repository, int $id, Request $request, MarkRepository $markRepository, EntityManagerInterface $entityManager): Response
    {
    $recipe = $repository->find($id);
    $mark = new Mark();
    $form = $this->createForm(MarkType::class, $mark);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $mark->setUser($this->getUser())
             ->setRecipe($recipe);

        $existingMark = $markRepository->findOneBy([
            'user' => $this->getUser(),
            'recipe' => $recipe
        ]);

        if (!$existingMark) {
            $entityManager->persist($mark);
        } else {
            dd($existingMark);
        }
        
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre note a été prise en compte avec succès.'
        );
        
        return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
    }

    return $this->render('recipe/show.html.twig', [
        'recipe' => $recipe,
        'form' => $form->createView()
    ]);
    }
   
      #[IsGranted('ROLE_USER')]
      #[Route('/createrecette', name: 'create')]
      public function create( Request $request, PersistenceManagerRegistry $doctrine)
        {
        $crud=new Recipe();
        $form=$this->createForm(RecipeType::class,$crud);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $crud=$form->getData();
            $crud->setUser($this->getUser());
            $em = $doctrine->getManager();
            $em->persist($crud);
            $em->flush();
            $this->addFlash('success',
                           'Votre recette a été crée avec succès !'
                 );
            return $this->redirectToRoute('recipe');
        }
        return $this->render('recipe/create.html.twig',[
            'form'=> $form->createView()
     ]);
    }

         #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
         #[Route('/update/{id}', name: 'update')]
         public function update( Request $request,$id, PersistenceManagerRegistry $doctrine)
        {
        $crud=$doctrine->getRepository(Recipe::class)->find($id);
        $form=$this->createForm(RecipeType::class,$crud);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($crud);
            $em->flush();
            $this->addFlash('success',
                           'Votre recette a été modifiée avec succès !'
                 );
            return $this->redirectToRoute('recipe');
        }
        return $this->render('recipe/update.html.twig',[
            'form'=> $form->createView()
     ]);
    }


      #[Route('/delete/{id}', name: 'delete')]
      public function delete($id, PersistenceManagerRegistry $doctrine)
        {
       $data=$doctrine->getRepository(Recipe::class)->find($id);
       $em=$doctrine->getManager();
       $em->remove($data);
       $em->flush();
       $this->addFlash('success',
                           'Votre recette a été supprimée avec succès !'
            );      
        return $this->redirectToRoute('recipe');

       }
}
