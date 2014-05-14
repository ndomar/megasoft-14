<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Symfony\Component\Validator\Constraints\DateTime;

/*
 * Fixtures for Delete Request Action in Request Controller
 * @author OmarElAzazy
 */
class LoadDeleteRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->addUsers($manager);
        $this->addSessions($manager);
        $this->addTangles($manager);
        $this->addUserTangles($manager);
        $this->addRequests($manager);
        $this->addOffers($manager);
    }

    /*
     * Helper function to add needed users to the database for the test
     * @param Doctrine\Common\Persistence\ObjectManager $manager The entity manager
     * @author OmarElAzazy
     */
    private function addUsers(ObjectManager $manager){
        $user1 = new User();
        $user1->setName('Omar');
        $user1->setPassword('password1');
        $manager->persist($user1);
        $this->setReference('user1', $user1);

        $user2 = new User();
        $user2->setName('Aly');
        $user2->setPassword('password2');
        $manager->persist($user2);
        $this->setReference('user2', $user2);

        $manager->flush();
    }

    /*
     * Helper function to add needed sessions to the database for the test
     * @param Doctrine\Common\Persistence\ObjectManager $manager The entity manager
     * @author OmarElAzazy
     */
    private function addSessions(ObjectManager $manager){
        $sessionUser1 = new Session();
        $sessionUser1->setCreated(new DateTime('now'));
        $sessionUser1->setExpired(false);
        $sessionUser1->setSessionId('sessionUser1');
        $sessionUser1->setUser($this->getReference('user1'));
        $sessionUser1->setDeviceType('Device 1');
        $manager->persist($sessionUser1);

        $sessionUser2 = new Session();
        $sessionUser2->setCreated(new DateTime('now'));
        $sessionUser2->setExpired(false);
        $sessionUser2->setSessionId('sessionUser2');
        $sessionUser2->setUser($this->getReference('user2'));
        $sessionUser2->setDeviceType('Device 2');
        $manager->persist($sessionUser2);

        $sessionUser1Expired = new Session();
        $sessionUser1Expired->setCreated(new DateTime('now'));
        $sessionUser1Expired->setExpired(true);
        $sessionUser1Expired->setSessionId('sessionUser1Expired');
        $sessionUser1Expired->setUser($this->getReference('user1'));
        $sessionUser1Expired->setDeviceType('Device 3');
        $manager->persist($sessionUser1Expired);

        $manager->flush();
    }

    /*
     * Helper function to add needed tangles to the database for the test
     * @param Doctrine\Common\Persistence\ObjectManager $manager The entity manager
     * @author OmarElAzazy
     */
    private function addTangles(ObjectManager $manager){
        $tangle = new Tangle();
        $tangle->setName('tangle');
        $tangle->setDeleted(false);
        $tangle->setDeletedBalance(0);
        $tangle->setDescription('Just a tangle');
        $tangle->setIcon('');
        $manager->persist($tangle);
        $this->setReference('tangle', $tangle);

        $manager->flush();
    }

    /*
     * Helper function to add needed user tangle relation to the database for the test
     * @param Doctrine\Common\Persistence\ObjectManager $manager The entity manager
     * @author OmarElAzazy
     */
    private function addUserTangles(ObjectManager $manager){
        $user1Tangle = new UserTangle();
        $user1Tangle->setCredit(0);
        $user1Tangle->setTangle($this->getReference('tangle'));
        $user1Tangle->setUser($this->getReference('user1'));
        $manager->persist($user1Tangle);

        $user2Tangle = new UserTangle();
        $user2Tangle->setCredit(0);
        $user2Tangle->setTangle($this->getReference('tangle'));
        $user2Tangle->setUser($this->getReference('user2'));
        $manager->persist($user2Tangle);

        $manager->flush();
    }

    /*
     * Helper function to add needed requests to the database for the test
     * @param Doctrine\Common\Persistence\ObjectManager $manager The entity manager
     * @author OmarElAzazy
     */
    private function addRequests(ObjectManager $manager){
        $request1 = new Request();
        $request1->setTangle($this->getReference('tangle'));
        $request1->setIcon('');
        $request1->setDescription('This is a request');
        $request1->setDeleted(false);
        $request1->setRequestedPrice(0);
        $request1->setStatus(0);
        $request1->setUser($this->getReference('user1'));
        $manager->persist($request1);
        $this->addReference('request1', $request1);

        $request2 = new Request();
        $request2->setTangle($this->getReference('tangle'));
        $request2->setIcon('');
        $request2->setDescription('This is a request');
        $request2->setDeleted(false);
        $request2->setRequestedPrice(0);
        $request2->setStatus(1);
        $request2->setUser($this->getReference('user1'));
        $manager->persist($request2);
        $this->addReference('request2', $request2);

        $request3 = new Request();
        $request3->setTangle($this->getReference('tangle'));
        $request3->setIcon('');
        $request3->setDescription('This is a request');
        $request3->setDeleted(true);
        $request3->setRequestedPrice(0);
        $request3->setStatus(0);
        $request3->setUser($this->getReference('user1'));
        $manager->persist($request3);
        $this->addReference('request3', $request3);

        $request4 = new Request();
        $request4->setTangle($this->getReference('tangle'));
        $request4->setIcon('');
        $request4->setDescription('This is a request');
        $request4->setDeleted(false);
        $request4->setRequestedPrice(0);
        $request4->setStatus(0);
        $request4->setUser($this->getReference('user2'));
        $manager->persist($request4);
        $this->addReference('request4', $request4);

        $manager->flush();
    }

    /*
     * Helper function to add needed offers to the database for the test
     * @param Doctrine\Common\Persistence\ObjectManager $manager The entity manager
     * @author OmarElAzazy
     */
    private function addOffers(ObjectManager $manager){
        $offer = new Offer();
        $offer->setDeleted(false);
        $offer->setUser($this->getReference('user2'));
        $offer->setStatus(0);
        $offer->setRequestedPrice(0);
        $offer->setRequest($this->getReference('request1'));
        $offer->setDate(new DateTime('now'));
        $offer->setUser($this->getReference('user2'));
        $offer->setDescription('This is an offer');
        $offer->setExpectedDeadline(new DateTime('now'));
        $manager->persist($offer);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}