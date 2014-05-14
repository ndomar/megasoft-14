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
        $manager->flush();

        $user1 = new User();
        $user1->setName('sampleUser1');
        $user1->setPassword('samplePassword1');

        $manager->persist($user1);
        $manager->flush();

        $user2 = new User();
        $user2->setName('sampleUser2');
        $user2->setPassword('samplePassword2');

        $manager->persist($user2);
        $manager->flush();

        $this->addReference('sampleUser', $user);
        $this->addReference('sampleUser1', $user1);
        $this->addReference('sampleUser2', $user2);


    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}