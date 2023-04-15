<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        // Utilisation de FakerPhp, pour dire d'avoir des données en français (rien à voir avec les fixtures)
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        // Je précise où je veux remplir les données
        /* J'utilise une boucle for pour créer 50 données
            Je crée un nouvel ingrédient (faker->word() est FakerPhp)
            Je lui donne un nom et un nombre aléatoire entre 0 et 100 */
        for ($i = 1; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                // $ingredient->setName('ingredient ' . $i)
                ->setPrice(mt_rand(0, 100));

            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}
