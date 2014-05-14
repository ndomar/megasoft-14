<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\Tangle;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Initialize the fixtures needed for the test.
 * @author Mansour
 */
class LoadCreateTangleData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('createTangleTestUser');
        $user->setPassword('createTangleTestUserPassword');
        $manager->persist($user);
        $manager->flush();
        $this->addReference('createTangleTestUser', $user);
        $session = new Session();
        $session->setUser($this->getReference('createTangleTestUser'));
        $session->setSessionId('CreateTangleTestSession');
        $session->setExpired(false);
        $session->setCreated(new DateTime('now'));
        $session->setDeviceType('Nexus7');
        $session->setRegId(1);
        $manager->persist($session);
        $manager->flush();
        $tangle = new Tangle();
        $tangle->setName('testTangle');
        $tangle->setIcon('1');
        $tangle->setDescription('testDescription');
        $manager->persist($tangle);
        $manager->flush();
        $this->addReference('createTangleSession', $session);
    }

    public function getOrder()
    {
        return 1;
    }

}
