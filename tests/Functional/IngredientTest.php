<?php

namespace App\Tests\Functional;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;



class IngredientTest extends WebTestCase
{
    public function testIfIngredientIsSuccessful(): void
    {
        $client = static::createClient();
        $urlGenerator=$client->getContainer()->get("router");
        $entityManager=$client->getContainer()->get('doctrine.orm.entity_manager');
        $user=$entityManager->find(User::class,12);
        $client->loginUser($user);
        $crawler=$client->request(Request::METHOD_GET,$urlGenerator->generate('createe'));
        $form=$crawler->filter('form[name=crud]')->form([
            'crud[name]'=>"IngredientPIzza",
            'crud[price]'=>floatval(33)
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('main');
    }
}
