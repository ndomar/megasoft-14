<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\UserTangle;

class LoadUserTangleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userTangle = new UserTangle();
        $userTangle->setCredit(0);
        $userTangle->setTangle($this->getReference('sampleTangle'));
        $userTangle->setUser($this->getReference('sampleUser'));
        $userTangle->setTangleOwner(true);
        
        $manager->persist($userTangle);
        $manager->flush();
       
        $this->addReference('sampleUserTangle', $userTangle);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}