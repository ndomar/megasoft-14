<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadSessionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $session = new Session();
        $session->setUser($this->getReference('sampleUser'));
        $session->setSessionId('sampleSession');
        $session->setExpired(false);
        $session->setCreated(new DateTime('now'));
        $session->setDeviceType('Microsoft Surface Pro');
        $session->setRegId(1);
        
        $manager->persist($session);
        $manager->flush();

        $session1 = new Session();
        $session1->setUser($this->getReference('sampleUser'));
        $session1->setSessionId('sampleSession1');
        $session1->setExpired(true);
        $session1->setCreated(new DateTime('now'));
        $session1->setDeviceType('Microsoft Surface Pro');
        $session1->setRegId(1);

        $manager->persist($session1);
        $manager->flush();

        $session2 = new Session();
        $session2->setUser($this->getReference('sampleUser1'));
        $session2->setSessionId('sampleSession2');
        $session2->setExpired(true);
        $session2->setCreated(new DateTime('now'));
        $session2->setDeviceType('Microsoft Surface Pro');
        $session2->setRegId(1);

        $manager->persist($session2);
        $manager->flush();

        $session3 = new Session();
        $session3->setUser($this->getReference('sampleUser2'));
        $session3->setSessionId('sampleSession3');
        $session3->setExpired(false);
        $session3->setCreated(new DateTime('now'));
        $session3->setDeviceType('Microsoft Surface Pro');
        $session3->setRegId(1);

        $manager->persist($session3);
        $manager->flush();

        $this->addReference('sampleSession', $session);
        $this->addReference('sampleSession1', $session1);
        $this->addReference('sampleSession2', $session2);
        $this->addReference('sampleSession3', $session3);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}