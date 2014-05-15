<?php


namespace Megasoft\EntangleBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\UserEmail;

class LoadUserEmailData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userEmail = new UserEmail();
        $userEmail->setUser($this->getReference('sampleUser'));
        $userEmail->setEmail('sample@sample.com');
        $userEmail->setVerified(1);

        $manager->persist($userEmail);
        $manager->flush();

        $userEmail2 = new UserEmail();
        $userEmail2->setUser($this->getReference('sampleUser2'));
        $userEmail2->setEmail('sample2@sample.com');
        $userEmail2->setVerified(1);

        $manager->persist($userEmail2);
        $manager->flush();

        $userEmail3 = new UserEmail();
        $userEmail3->setUser($this->getReference('sampleUser3'));
        $userEmail3->setEmail('sample3@sample.com');
        $userEmail3->setVerified(1);

        $manager->persist($userEmail3);
        $manager->flush();

        $this->addReference('sampleUserEmail', $userEmail);
        $this->addReference('sampleUserEmail2', $userEmail2);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}