<?php


namespace Megasoft\EntangleBundle\DataFixtures\ORM;


use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;

class LoadReopenRequestActionData extends AbstractFixture implements OrderedFixtureInterface
{

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

        $user3 = new User();
        $user3->setName('sampleUser3');
        $user3->setPassword('samplePassword');
        $manager->persist($user3);

        $tangle = new Tangle();
        $tangle->setName('sampleTangle');
        $tangle->setDescription('Just a sample tangle');
        $manager->persist($tangle);


        $session1 = new Session();
        $session1->setUser($user1);
        $session1->setSessionId('sampleSession');
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

        $session4 = new Session();
        $session4->setUser($user3);
        $session4->setSessionId('sampleSession4');
        $session4->setExpired(false);
        $session4->setCreated(new DateTime('now'));
        $session4->setDeviceType('Microsoft Surface Pro');
        $session4->setRegId(1);
        $manager->persist($session4);

        $userTangle1 = new UserTangle();
        $userTangle1->setCredit(0);
        $userTangle1->setTangle($tangle);
        $userTangle1->setUser($user1);
        $userTangle1->setTangleOwner(true);
        $manager->persist($userTangle1);

        $userTangle2 = new UserTangle();
        $userTangle2->setCredit(0);
        $userTangle2->setTangle($tangle);
        $userTangle2->setUser($user2);
        $userTangle2->setTangleOwner(false);
        $manager->persist($userTangle2);

        $userTangle3 = new UserTangle();
        $userTangle3->setCredit(0);
        $userTangle3->setTangle($tangle);
        $userTangle3->setUser($user3);
        $userTangle3->setTangleOwner(false);
        $manager->persist($userTangle3);

        $request1 = new Request();
        $request1->setUser($user2);
        $request1->setDate(new \DateTime("now"));
        $request1->setTangle($tangle);
        $request1->setDescription("Description");
        $request1->setStatus(0);
        $manager->persist($request1);

        $request2 = new Request();
        $request2->setUser($user2);
        $request2->setDate(new \DateTime("now"));
        $request2->setTangle($tangle);
        $request2->setDescription("Description");
        $request2->setStatus(1);
        $manager->persist($request2);

        $request3 = new Request();
        $request3->setUser($user2);
        $request3->setDate(new \DateTime("now"));
        $request3->setTangle($tangle);
        $request3->setDescription("Description");
        $request3->setStatus(2);
        $manager->persist($request3);

        $this->addReference('sampleUserTangle1', $userTangle1);
        $this->addReference('sampleUserTangle2', $userTangle2);
        $this->addReference('sampleUserTangle3', $userTangle3);
        $this->addReference('sampleSession1', $session1);
        $this->addReference('sampleSession2', $session2);
        $this->addReference('sampleSession3', $session3);
        $this->addReference('sampleSession4', $session4);
        $this->addReference('sampleTangle', $tangle);
        $this->addReference('sampleUser1', $user1);
        $this->addReference('sampleUser2', $user2);
        $this->addReference('sampleUser3', $user3);
        $this->addReference('sampleRequest1', $request1);
        $this->addReference('sampleRequest2', $request2);
        $this->addReference('sampleRequest3', $request3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
} 