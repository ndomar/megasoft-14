<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\VerificationCode;
use Megasoft\EntangleBundle\Entity\UserEmail;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadVerificationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('sampleUser');
        $user->setPassword('samplePassword');
        $date = new \DateTime("now");
        $userEmail = new UserEmail();
        $userEmail->setUser($user);
        $userEmail->setEmail('sampleEmail@sample.com');
        $verificationCode = new VerificationCode();
        $verificationCode->setUserEmail($userEmail);
        $verificationCode->setVerificationCode('123456');
        $verificationCode->setCreated($date);
        $verificationCode->setExpired(false);
        $user2 = new User();
        $user2->setName('sampleUser1');
        $user2->setPassword('samplePassword');
        $date = new \DateTime("now");
        $userEmail2 = new UserEmail();
        $userEmail2->setUser($user2);
        $userEmail2->setEmail('sampleEmail1@sample.com');
        $verificationCode2 = new VerificationCode();
        $verificationCode2->setUserEmail($userEmail2);
        $verificationCode2->setVerificationCode('1234567');
        $verificationCode2->setCreated($date);
        $verificationCode2->setExpired(true);

        $manager->persist($user);
        $manager->persist($verificationCode);
        $manager->persist($userEmail);
        $manager->persist($user2);
        $manager->persist($verificationCode2);
        $manager->persist($userEmail2);
        $manager->flush();
        $this->addReference('sampleUser', $user);
        $this->addReference('123456', $verificationCode);
        $this->addReference('sampleEmail@sample.com', $userEmail);
        $this->addReference('sampleUser1', $user2);
        $this->addReference('1234567', $verificationCode2);
        $this->addReference('sampleEmail1@sample.com', $userEmail2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}