<?php

namespace App\DataFixtures;

use App\Entity\Promo;
use App\Entity\Section;
use App\Repository\PromoRepository;
use App\Repository\SectionRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime as DateTimeProvider;
use Faker\Provider\en_US\Company;
use Proxies\__CG__\App\Entity\User;

class AppFixtures extends Fixture
{
    private $promoRepository;

    public function __construct(PromoRepository $promoRepository)
    {
        $this->promoRepository = $promoRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');
        $faker->addProvider(new Company($faker));
        $faker->addProvider(new DateTimeProvider($faker));

        $jobWords = ['engineer', 'developer', 'manager', 'designer', 'analyst', 'consultant', 'specialist'];

        for ($i = 0; $i < 10; $i++) {
            $section = new Section();
            $randomJobWord = $jobWords[array_rand($jobWords)];
            $section->setNomSection($randomJobWord);
            $section->setAbreviation($faker->word());
            $manager->persist($section);

            for ($j = 0; $j < rand(1, 3); $j++) {
                $promo = new Promo();
                $promo->setSection($section);
                $promo->setDateDebut($faker->dateTimeBetween('-5 years', 'now'));
                $promo->setDateFin($faker->dateTimeBetween('now', '+5 years'));
                $promo->setNomPromo($faker->year());
                $manager->persist($promo);
            }
        }

        $manager->flush();

        // // Loop through every promo using promoRepo
        // $promos = $this->promoRepository->findAll();
        // foreach ($promos as $promo) {
        //     $user = new User();
        //     $user->setEmail($faker->email());
        //     $user->getPassword("Test.123");
        //     $user->setIdentifiant()
        // }
    }
}
