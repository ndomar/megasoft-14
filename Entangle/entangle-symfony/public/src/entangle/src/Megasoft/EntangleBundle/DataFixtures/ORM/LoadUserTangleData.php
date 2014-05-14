<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\UserTangle;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
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

        $userTangle1 = new UserTangle();
        $userTangle1->setCredit(0);
        $userTangle1->setTangle($this->getReference('sampleTangle'));
        $userTangle1->setUser($this->getReference('sampleUser1'));
        $userTangle1->setTangleOwner(false);

        $manager->persist($userTangle1);
        $manager->flush();

        $userTangle2 = new UserTangle();
        $userTangle2->setCredit(10);
        $userTangle2->setTangle($this->getReference('sampleTangle1'));
        $userTangle2->setUser($this->getReference('sampleUser2'));
        $userTangle2->setTangleOwner(true);

        $manager->persist($userTangle2);
        $manager->flush();

        $this->addReference('sampleUserTangle', $userTangle);
        $this->addReference('sampleUserTangle1', $userTangle1);
        $this->addReference('sampleUserTangle2', $userTangle2);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }
}