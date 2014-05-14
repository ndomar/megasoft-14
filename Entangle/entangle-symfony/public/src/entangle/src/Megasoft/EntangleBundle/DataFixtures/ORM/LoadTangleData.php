<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Tangle;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadTangleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $tangle = new Tangle();
        $tangle->setName('sampleTangle');
        $tangle->setDescription('Just a sample tangle');
        
        $manager->persist($tangle);
        $manager->flush();
        
        $this->addReference('sampleTangle', $tangle);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}