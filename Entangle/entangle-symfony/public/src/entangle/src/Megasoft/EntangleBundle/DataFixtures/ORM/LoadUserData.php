<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\User;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('sampleUser');
        $user->setPassword('samplePassword');
        
        $manager->persist($user);

        $user1 = new User();
        $user1->setName('sampleUser1');
        $user1->setPassword('samplePassword1');

        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('sampleUser2');
        $user2->setPassword('samplePassword2');

        $manager->persist($user2);
        $manager->flush();

        $user3 = new User();
        $user3->setName('sampleUser3');
        $user3->setPassword('samplePassword3');

        $manager->persist($user3);
        $manager->flush();
        
        $this->addReference('sampleUser', $user);
        $this->addReference('sampleUser1', $user1);
        $this->addReference('sampleUser2', $user2);
        $this->addReference('sampleUser3', $user3);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
