<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
     /**
      * @var Generator
      */
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
           // users
         $users=[];
         $admin= new User();
        $admin->setFullName('Administrateur des recettes')
            ->setPseudo(null)
            ->setEmail('imane@gmail.fr')
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPlainPassword('password');

            $users[]= $admin;
            $manager->persist($admin);

         for($i=0; $i<10 ; $i++){
        $user= new User();
        $user->setFullName($this->faker->name())
            ->setPseudo(mt_rand(0,1) === 1 ? $this->faker->firstName():null)
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('password');

            $users[]= $user;
            $manager->persist($user);
          }

        // ingredients
         $ingredients=[];
        for($i =0 ; $i<50 ; $i++){
        $ingredient=new Ingredient();
        $ingredient->setName($this->faker->word())
        ->setPrice(mt_rand(0,100))
       ->setUser($users[mt_rand(0,count($users)-1)]);
        $ingredients[]= $ingredient;
        $manager->persist($ingredient);

       }


       //recipes
       $recipes=[];
       for($j = 0; $j<25 ;$j++){
        $recipe= new Recipe();
        $recipe->setName($this->faker->word())  
        ->setTime(mt_rand(0,1) == 1 ? mt_rand(1,1440):null)
        ->setNbrpersonne(mt_rand(0,1) == 1 ? mt_rand(1,50):null)
        ->setDifficulte(mt_rand(0,1) == 1 ? mt_rand(1,5):null)
        ->setDescription($this->faker->text(300))  
        ->setPrice(mt_rand(0,1) == 1 ? mt_rand(1,1000):null)
        ->setIsFavorite(mt_rand(0,1) == 1 ? true: false)
        ->setIsPublic(mt_rand(0,1) == 1 ? true: false)
        ->setUser($users[mt_rand(0,count($users)-1)]);

        for($k = 0; $k < mt_rand(5,15); $k++ ){
            $recipe->addIngredient($ingredients[mt_rand(0,count($ingredients)-1)]);
        }
        $recipes[]= $recipe;
        $manager->persist($recipe);
    }

   
  }
   }
