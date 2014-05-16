<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\Session;
use Symfony\Component\Validator\Constraints\DateTime;

/*
 * Fixtures for UserEmail table
 * @author maisaraFarahat
 */

class LoadUserEditData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('maisara');
        $user->setPassword('meso1993');

        $user->addEmail('maisara.farahat@gmail.com');
        $user->setUserBio('lola el hawa ma kont geet');
        $user->setBirthDate('29-04-1993 00:00:00');

        $manager->persist($user);

        $session = new Session();

        $session->setCreated(new DateTime('now'));
        $session->setDeviceType('to Avoid Null');
        $session->setExpired(0);
        $session->setRegId('safsad');
        $session->setUserId($user->getId());

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
