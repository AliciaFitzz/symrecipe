<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        // vérifie s'il y a bien un éléments avec les classes mentionnée
        $button = $crawler->filter('.btn.btn-primary.btn-lg');
        $this->assertEquals(1, count($button)); // on en attend 1

        $recipes = $crawler->filter('.recipes .card');
        $this->assertEquals(3, count($recipes));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur SymRecipe');
    }
}
