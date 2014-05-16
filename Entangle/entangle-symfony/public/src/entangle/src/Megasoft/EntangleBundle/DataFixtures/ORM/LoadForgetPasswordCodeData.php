<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\ForgetPasswordCode;
use Megasoft\EntangleBundle\Entity\User;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadForgetPasswordCodeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $forgetPassCode = new ForgetPasswordCode();
        $forgetPassCode->setUser($this->getReference('sampleUser'));
        $forgetPassCode->setForgetPasswordCode("5wshJEh6dPU2MT8PFJlp2VMngyXImF");
        $forgetPassCode->setExpired(0);
        $forgetPassCode->setCreated(new \DateTime('now'));

        $manager->persist($forgetPassCode);
        $manager->flush();

        $forgetPassCode2 = new ForgetPasswordCode();
        $forgetPassCode2->setUser($this->getReference('sampleUser3'));
        $forgetPassCode2->setForgetPasswordCode("thisISaSAMPLEpasswordCODE");
        $forgetPassCode2->setExpired(1);
        $forgetPassCode2->setCreated(new \DateTime('now'));

        $manager->persist($forgetPassCode2);
        $manager->flush();

        $this->addReference('forgetPassCode',$forgetPassCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}