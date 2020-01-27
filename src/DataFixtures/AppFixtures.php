<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use App\Entity\Colors;
use App\Entity\Brand;
use App\Entity\Cars;

class AppFixtures extends Fixture
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
      $faker = Faker\Factory::create('fr_FR');
      $faker->addProvider(new \Faker\Provider\Fakecar($faker));

      $colors = [];
      for ($i=0; $i < 20; $i++) {
          $colors[$i] = new Colors();
          $colors[$i]->setName($faker->colorName)
              ->setReferance($faker->hexcolor);
          $manager->persist($colors[$i]);
      }

      $brand = [];
      for ($i=0; $i < 50; $i++) {
          $brand[$i] = new Brand();
          $vehicle = $faker->vehicleArray;
          $brand[$i]->setName($vehicle['brand']);

          $cars[$i] = new Cars();
          $cars[$i]->setName($vehicle['brand'] .' '. $vehicle['model']);
          $cars[$i]->setModel($vehicle['model']);
          $cars[$i]->setDescription($vehicle['brand']);
          $cars[$i]->setBrand($brand[$i]);
          $cars[$i]->setColors($colors[rand(1,19)]);
          $cars[$i]->setYear($faker->biasedNumberBetween(1998,2019, 'sqrt'));

          $manager->persist($brand[$i]);
          $manager->persist($cars[$i]);
      }

      $manager->flush();
    }
}
