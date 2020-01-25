<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AccountFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $account1 = new Account();
        $account1->setUserId(1)->setAmount('100.00');
        $manager->persist($account1);

        $account2 = new Account();
        $account2->setUserId(2)->setAmount('50.00');
        $manager->persist($account2);

        $manager->flush();
    }
}
