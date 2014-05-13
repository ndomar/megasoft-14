<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;

/*
 * Fixtures for Session table
 * @author OmarElAzazy
 */
class LoadFilterStreamData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $user1 = new User();
        $user1->setName('sampleUser1');
        $user1->setPassword('samplePassword');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('sampleUser2');
        $user2->setPassword('samplePassword');
        $manager->persist($user2);

        $tangle = new Tangle();
        $tangle->setName('sampleTangle');
        $tangle->setDescription('Just a sample tangle');
        $manager->persist($tangle);

        $session1 = new Session();
        $session1->setUser($user1);
        $session1->setSessionId('sampleSession1');
        $session1->setExpired(false);
        $session1->setCreated(new DateTime('now'));
        $session1->setDeviceType('Microsoft Surface Pro');
        $session1->setRegId(1);
        $manager->persist($session1);

        $session2 = new Session();
        $session2->setUser($user2);
        $session2->setSessionId('sampleSession2');
        $session2->setExpired(false);
        $session2->setCreated(new DateTime('now'));
        $session2->setDeviceType('Microsoft Surface Pro');
        $session2->setRegId(1);
        $manager->persist($session2);

        $session3 = new Session();
        $session3->setUser($user2);
        $session3->setSessionId('sampleSession3');
        $session3->setExpired(true);
        $session3->setCreated(new DateTime('now'));
        $session3->setDeviceType('Microsoft Surface Pro');
        $session3->setRegId(1);
        $manager->persist($session3);

        $userTangle = new UserTangle();
        $userTangle->setCredit(0);
        $userTangle->setTangle($tangle);
        $userTangle->setUser($user1);
        $userTangle->setTangleOwner(true);
        $manager->persist($userTangle);

        $request1 = new Request();
        $request1->setUser($user1)
                ->setDate(new \DateTime("now"))
                ->setTangle($tangle)
                ->setDescription("Description")
                ->setStatus(0);

        $request2 = new Request();
        $request2->setUser($user1)
                ->setDate(new \DateTime("now"))
                ->setTangle($tangle)
                ->setDescription("Description")
                ->setStatus(0);

        $request3 = new Request();
        $request3->setUser($user2)
                ->setDate(new \DateTime("now"))
                ->setTangle($tangle)
                ->setDescription("Description")
                ->setStatus(0)
                ->setDeleted(true);

        $request4 = new Request();
        $request4->setUser($user1)
                 ->setDate(new \DateTime("now"))
                 ->setTangle($tangle)
                 ->setDescription("Description")
                 ->setStatus(1);

        $request5 = new Request();
        $request5->setUser($user1)
                ->setDate(new \DateTime("now"))
                ->setTangle($tangle)
                ->setDescription("No")
                ->setStatus(0);

        $manager->persist($request1);
        $manager->persist($request2);
        $manager->persist($request3);
        $manager->persist($request4);
        $manager->persist($request5);


        $this->addReference('sampleUserTangle', $userTangle);
        $this->addReference('sampleSession1', $session1);
        $this->addReference('sampleSession2', $session2);
        $this->addReference('sampleSession3', $session3);
        $this->addReference('sampleTangle', $tangle);
        $this->addReference('sampleUser1', $user1);
        $this->addReference('sampleUser2', $user2);

        $manager->flush();


    }
}