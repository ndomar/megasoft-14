<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;

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
        
        $manager->persist($session);
        $manager->flush();
        
        $this->addReference('sampleSession', $session);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}