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

        $tangle1 = new Tangle();
        $tangle1->setName('sampleTangle1');
        $tangle1->setDescription('Another sample tangle');

        $manager->persist($tangle1);
        $manager->flush();

        $this->addReference('sampleTangle', $tangle);
        $this->addReference('sampleTangle1', $tangle1);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}