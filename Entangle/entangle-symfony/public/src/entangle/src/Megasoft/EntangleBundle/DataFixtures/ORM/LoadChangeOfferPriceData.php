<?php


namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;


class LoadChangeOfferPriceData extends AbstractFixture implements OrderedFixtureInterface
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
        $session1->setUser($user2);
        $session1->setSessionId('sampleSession');
        $session1->setExpired(false);
        $session1->setCreated(new DateTime('now'));
        $session1->setDeviceType('Microsoft Surface Pro');
        $session1->setRegId(1);
        $manager->persist($session1);

        $session2 = new Session();
        $session2->setUser($user2);
        $session2->setSessionId('sampleSession2');
        $session2->setExpired(true);
        $session2->setCreated(new DateTime('now'));
        $session2->setDeviceType('Microsoft Surface Pro');
        $session2->setRegId(1);
        $manager->persist($session2);

        $session3 = new Session();
        $session3->setUser($user3);
        $session3->setSessionId('sampleSession3');
        $session3->setExpired(false);
        $session3->setCreated(new DateTime('now'));
        $session3->setDeviceType('Microsoft Surface Pro');
        $session3->setRegId(1);
        $manager->persist($session3);

        $userTangle1 = new UserTangle();
        $userTangle1->setCredit(0);
        $userTangle1->setTangle($tangle);
        $userTangle1->setUser($user1);
        $userTangle1->setTangleOwner(true);
        $manager->persist($userTangle1);

        $userTangle2 = new UserTangle();
        $userTangle2->setCredit(0)
            ->setTangle($tangle)
            ->setUser($user2)
            ->setTangleOwner(false);
        $manager->persist($userTangle2);

        $userTangle3 = new UserTangle();
        $userTangle3->setCredit(0)
            ->setTangle($tangle)
            ->setUser($user3)
            ->setTangleOwner(false);
        $manager->persist($userTangle3);

        $request = new Request();
        $request->setUser($user1)
            ->setDate(new \DateTime("now"))
            ->setTangle($tangle)
            ->setDescription("Description")
            ->setStatus(0);
        $manager->persist($request);

        $offer1 = new Offer();
        $offer1->setRequest($request)
            ->setDate(new \DateTime("now"))
            ->setDescription('SampleOffer1')
            ->setStatus(0)
            ->setUser($user2)
            ->setRequestedPrice(500);
        $manager->persist($offer1);

        $offer2 = new Offer();
        $offer2->setRequest($request)
            ->setDate(new \DateTime("now"))
            ->setDescription('SampleOffer2')
            ->setStatus(1)
            ->setUser($user2)
            ->setRequestedPrice(500);
        $manager->persist($offer2);

        $offer3 = new Offer();
        $offer3->setRequest($request)
            ->setDate(new \DateTime("now"))
            ->setDescription('SampleOffer2')
            ->setStatus(2)
            ->setUser($user2)
            ->setRequestedPrice(500);
        $manager->persist($offer3);

        $offer4 = new Offer();
        $offer4->setRequest($request)
            ->setDate(new \DateTime("now"))
            ->setDescription('SampleOffer4')
            ->setStatus(3)
            ->setUser($user2)
            ->setRequestedPrice(500);
        $manager->persist($offer4);

        $offer5 = new Offer();
        $offer5->setRequest($request)
            ->setDate(new \DateTime("now"))
            ->setDescription('SampleOffer5')
            ->setStatus(4)
            ->setUser($user2)
            ->setRequestedPrice(500);
        $manager->persist($offer5);

        $this->addReference('sampleUserTangle1', $userTangle1);
        $this->addReference('sampleUserTangle2', $userTangle2);
        $this->addReference('sampleUserTangle3', $userTangle3);
        $this->addReference('sampleSession1', $session1);
        $this->addReference('sampleSession2', $session2);
        $this->addReference('sampleSession3', $session3);
        $this->addReference('sampleTangle', $tangle);
        $this->addReference('sampleUser1', $user1);
        $this->addReference('sampleUser2', $user2);
        $this->addReference('sampleUser3', $user3);
        $this->addReference('sampleRequest', $request);
        $this->addReference('sampleOffer1', $offer1);
        $this->addReference('sampleOffer2', $offer2);
        $this->addReference('sampleOffer3', $offer3);
        $this->addReference('sampleOffer4', $offer4);
        $this->addReference('sampleOffer5', $offer5);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
} 