<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\User;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('sampleUser');
        $user->setPassword('samplePassword');
        $verificationCode = new VerificationCode();
        $verificationCode->setUser($user);
        $verificationCode->setVerificationCode('123456');
        $userEmail = new UserEmail();
        $userEmail->setUser($user);
        $userEmail->setEmail('mahmoudgamaleid@gmail.com');
        $manager->persist($user);
        $manager->persist($verificationCode);
        $manager->persist($userEmail);
        $manager->flush();
        $this->addReference('sampleUser', $user);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}