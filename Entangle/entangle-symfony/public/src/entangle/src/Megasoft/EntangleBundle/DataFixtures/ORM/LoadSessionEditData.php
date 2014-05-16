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

class LoadSessionEditData extends AbstractFixture implements OrderedFixtureInterface
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

        $session1 = new Session();
        $session1->setUser($this->getReference('sampleUser'));
        $session1->setSessionId('sampleSession1');
        $session1->setExpired(true);
        $session1->setCreated(new DateTime('now'));
        $session1->setDeviceType('Microsoft Surface Pro');
        $session1->setRegId(1);

        $manager->persist($session1);
        $manager->flush();

        $this->addReference('sampleSession', $session);
        $this->addReference('sampleSession1', $session1);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
