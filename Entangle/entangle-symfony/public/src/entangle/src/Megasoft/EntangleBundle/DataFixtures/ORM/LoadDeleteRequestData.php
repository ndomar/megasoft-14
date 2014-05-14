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

    private function addSessions(ObjectManager $manager){
        $sessionUser1 = new Session();
        $sessionUser1->setCreated(new DateTime('now'));
        $sessionUser1->setExpired(false);
        $sessionUser1->setSessionId('sessionUser1');
        $sessionUser1->setUser($this->getReference('user1'));
        $sessionUser1->setDeviceType('Device 1');
        $manager->persist($sessionUser1);

        $sessionUser2 = new Session();
        $sessionUser2->setCreated(new DateTime('now'));
        $sessionUser2->setExpired(false);
        $sessionUser2->setSessionId('sessionUser2');
        $sessionUser2->setUser($this->getReference('user2'));
        $sessionUser2->setDeviceType('Device 2');
        $manager->persist($sessionUser2);

        $sessionUser1Expired = new Session();
        $sessionUser1Expired->setCreated(new DateTime('now'));
        $sessionUser1Expired->setExpired(true);
        $sessionUser1Expired->setSessionId('sessionUser1Expired');
        $sessionUser1Expired->setUser($this->getReference('user1'));
        $sessionUser1Expired->setDeviceType('Device 3');
        $manager->persist($sessionUser1Expired);

        $manager->flush();
    }

    private function addTangles(ObjectManager $manager){
        $tangle = new Tangle();
        $tangle->setName('tangle');
        $tangle->setDeleted(false);
        $tangle->setDeletedBalance(0);
        $tangle->setDescription('Just a tangle');
        $tangle->setIcon('');
        $manager->persist($tangle);
        $this->setReference('tangle', $tangle);

        $manager->flush();
    }

    private function addUserTangles(ObjectManager $manager){
        $user1Tangle = new UserTangle();
        $user1Tangle->setCredit(0);
        $user1Tangle->setTangle($this->getReference('tangle'));
        $user1Tangle->setUser($this->getReference('user1'));
        $manager->persist($user1Tangle);

        $user2Tangle = new UserTangle();
        $user2Tangle->setCredit(0);
        $user2Tangle->setTangle($this->getReference('tangle'));
        $user2Tangle->setUser($this->getReference('user2'));
        $manager->persist($user2Tangle);

        $manager->flush();
    }
}