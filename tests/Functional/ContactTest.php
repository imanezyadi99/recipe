<?php
namespace App\Tests\Functional;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        /*$this->assertSelectorTextContains('h1', 'Hello World');*/

       /* $submit=$crawler->selectButton('Soumettre ma demande');*/
        $submitButton = $crawler->selectButton('Créer un contact');
        
        $form=$submitButton->form();

        $form["contact[FullName]"]="ImaneZyadi";
        $form["contact[email]"]="Imane11@gmail.com";
        $form["contact[sujet]"]="Test";
        $form["contact[message]"]="Test";

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertEmailCount(1);
        $client->followRedirect();
        /*$this->assertSelectorTextContains(
            'div.alert.alert-success',
            'Votre demande a été envoyée avec succès !'
    ):*/
    }
}
