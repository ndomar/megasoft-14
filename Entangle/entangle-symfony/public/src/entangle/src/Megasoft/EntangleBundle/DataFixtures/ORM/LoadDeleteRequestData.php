<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Symfony\Component\Validator\Constraints\DateTime;

/*
 * Fixtures for Delete Request Action in Request Controller
 * @author OmarElAzazy
 */
class LoadUserTangleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->addUsers($manager);
        $this->addSessions($manager);
        $this->addTangles($manager);
        $this->addUserTangles($manager);
    }

    private function addUsers(ObjectManager $manager){
        $user1 = new User();
        $user1->setName('Omar');
        $user1->setPassword('password1');
        $manager->persist($user1);
        $this->setReference('user1', $user1);

        $user2 = new User();
        $user2->setName('Aly');
        $user2->setPassword('password2');
        $manager->persist($user2);
        $this->setReference('user2', $user2);

        $manager->flush();
    }

    
}