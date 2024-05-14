<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserInfo;
use App\Repository\PromoRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime as DateTimeProvider;
use Faker\Provider\en_US\Company;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $promoRepository;
    private $passwordHasher;
    //Declare les variables à inserer
    public function __construct(PromoRepository $promoRepository,  UserPasswordHasherInterface $passwordHasher)
    {
        $this->promoRepository = $promoRepository;
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        //Library Faker
        $faker = Factory::create('en_US');
        $faker->addProvider(new Company($faker));
        $faker->addProvider(new DateTimeProvider($faker));

        //On prend toutes les promotions de stagiaire
        $promos = $this->promoRepository->findAll();
        foreach ($promos as $promoEn) {
            //on cree 4 à 12 stagiaire par promo
            for ($h = 0; $h < rand(4, 12); $h++) {
                $identifiant = $this->generateUniqueIdentifier();
                //User
                $user = new User();
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    "Test.123"
                );
                $user->setEmail($faker->email());
                $user->setPassword($hashedPassword);
                $user->setIdentifiant($identifiant);
                $user->setRoles(['ROLE_STAGIAIRE']);
                $manager->persist($user);
                //UserInfo
                $userInfo = new UserInfo;
                $userInfo->setNom($faker->firstName());
                $userInfo->setPrenom($faker->lastName());
                $userInfo->setDateDeNaissance($faker->dateTimeBetween('-70 years', '-18 years'));
                $userInfo->setUser($user);
                $userInfo->setPromo($promoEn);
                $userInfo->setMontantGlobal(0);
                $manager->persist($userInfo);
            }
        }
        $manager->flush();
    }
    private function generateUniqueIdentifier()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $identifier = '';

        // Generate a unique identifier that follows the regex pattern and is 7 characters long
        do {
            $identifier = substr(str_shuffle($characters), 0, 7);
        } while (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $identifier));

        return $identifier;
        
    }
}
