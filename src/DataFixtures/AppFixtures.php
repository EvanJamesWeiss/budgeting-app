<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userA = new User();
        $userA->setName('User A');
        $manager->persist($userA);

        $userB = new User();
        $userB->setName('User B');
        $manager->persist($userB);

        $manager->flush();
    }
}
