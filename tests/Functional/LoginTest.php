<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessfull(): void
    {
        $client = static::createClient();

        // Répurère la route par le générateur d'URL
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        // Gérer le formulaire
        $form = $crawler->filter('form[name=login]')->form([
            "_username" => 'admin@symrecipe.fr',
            '_password' => 'password'
        ]);

        $client->submit($form);

        // Redirect page d'accueil
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('app_home');
    }

    public function testIfLoginFailedWhenPasswordIsWrong(): void
    {
        $client = static::createClient();

        // Répurère la route par le générateur d'URL
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        // Gérer le formulaire
        $form = $crawler->filter('form[name=login]')->form([
            "_username" => 'admin@symrecipe.fr',
            '_password' => 'password'
        ]);

        $client->submit($form);

        // Redirect page d'accueil
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('app_home');
        $this->assertSelectorTextContains('div.alert-danger', 'Invalid credentials.');
    }
}
