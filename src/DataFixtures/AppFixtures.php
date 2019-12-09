<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /** @var UserPasswordEncoderInterface  */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');



        for($u = 0;$u<10; $u++){
            $user = new User();
            $user->setEmail($faker->email)
                ->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setPassword($faker->password);

            $chrono = 1;

            for($i = 0; $i < mt_rand(1, 20); $i++){
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setCompany($faker->company)
                    ->setEmail( $this->encoder->encodePassword($user, $faker->email));

                $manager->persist($customer);

                $user->addCustomer($customer);

                for($j = 0; $j < mt_rand(3,10);$j++){
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2,250,5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT','PAID','CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono);
                    $manager->persist($invoice);
                    $chrono++;
                }
                $manager->persist($user);
            }

        }

        $manager->flush();
    }
}
