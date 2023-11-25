<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
public function getEntity(): Recipe
    {
        return (new Recipe())
        ->setName('Recipe #1')
        ->setDescription('Description #1')
        ->setIsFavorite(true)
        ->setcreatedAt(new \DateTimeImmutable())
        ->setupdatedAt(new \DateTimeImmutable());

    }

    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container=static::getContainer();
        $recipe=$this->getEntity();
        $errors=$container->get('validator')->validate($recipe);
        $this->assertCount(0,$errors);
    }

    public function testInvalidName()
    {
        self::bootKernel();
        $container=static::getContainer();
        $recipe=$this->getEntity();
        $recipe->setName('');
        $errors=$container->get('validator')->validate($recipe);
        $this->assertCount(2,$errors);
    }

    

    /*public function testGetAverage()
    {
        $recipe=$this->getEntity();
        $user=static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class,1);
        for($i=0;$i<5;$i++){
        $mark=new Mark();
        $mark->setMark(2)
              ->setUser($user)
              ->setRecipe($recipe);
        $recipe->addMark($mark);
      }
        $this->assertTrue(2.0 === $recipe->getAverage());
    }*/


    public function testGetAverage()
{
    $recipe = $this->getEntity();
    $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

    // Find a User entity by a different ID that exists in your test database
    $user = $entityManager->getRepository(User::class)->find(1);

    if ($user) {
        for ($i = 0; $i < 5; $i++) {
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);
            $recipe->addMark($mark);
        }

        // Calculate the expected average (5 marks with value 2)
        $expectedAverage = 2.0;

        // Assert that the calculated average matches the expected average
        $this->assertEquals($expectedAverage, $recipe->getAverage());
    } else {
        // If the user doesn't exist, mark the test as skipped or incomplete
        $this->markTestSkipped('User with ID 1 does not exist.');
    }
}








   

}
