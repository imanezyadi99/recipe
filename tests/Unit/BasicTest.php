<?php
namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        // Use $client to perform functional requests and assertions

        $this->assertTrue(true);
    }
}
