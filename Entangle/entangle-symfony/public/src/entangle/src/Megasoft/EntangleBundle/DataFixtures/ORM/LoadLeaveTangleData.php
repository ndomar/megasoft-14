<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Tangle;

/*
 * Fixtures for Leaving Tangle End-point
 */
class LoadLeaveTangleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->createUser($manager, 'Ahmad', 'ahmad');
        $this->createUser($manager, 'Mohamed', 'mohamed');
        $this->createUser($manager, 'Aly', 'aly');
        
        $this->createSession($manager, 'userAhmad', 'userAhmad', false, 1);
        $this->createSession($manager, 'userMohamed', 'userMohamed', true, 2);
        $this->createSession($manager, 'userAly', 'userAly', false, 3);
        
        $this->createTangle($manager);
        
        $this->createUserTangle($manager, 'userAhmad', true, 0);
        $this->createUserTangle($manager, 'userMohamed', false, 80);
        $this->createUserTangle($manager, 'userAly', false, -80);
        
        $this->createRequest($manager, 'userMohamed', 'i want to buy a car', 1);
        $this->createRequest($manager, 'userMohamed', 'i want to travel to London', 2);
        $this->createRequest($manager, 'userMohamed', 'i want to go to the doctor', 3);
        $this->createRequest($manager, 'userAly', 'i want to buy a book', 4);
        $this->createRequest($manager, 'userMohamed', 'i want to have a reminder software', 5);
        $this->createRequest($manager, 'userAly', 'i want to have a ride tomorrow', 6);
        
        $this->createOffer($manager, 'userAhmad', 'i can help', 1);
        $this->createOffer($manager, 'userMohamed', 'i want to help', 2);
        $this->createOffer($manager, 'userAly', 'i can do this for you', 3);
        $this->createOffer($manager, 'userAhmad', 'i will help you', 1);
                       
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
    
    private function createUser(ObjectManager $manager, $name, $password) {
        $user = new User();
        $user->setName($name);
        $user->setPassword($password);
        
        $manager->persist($user);
        $this->addReference('user' . "$name", $user);
    }
    
    
    private function createSession(ObjectManager $manager, $reference, $sessionId, $expired, $regId) {
        $session = new Session();
        $session->setUser($this->getReference("$reference"));
        $session->setSessionId("$sessionId");
        $session->setExpired($expired);
        $session->setCreated(new DateTime('now'));
        $session->setDeviceType('Samsung S4');
        $session->setRegId($regId);
        
        $manager->persist($session);
        $this->addReference('session' . "$reference", $session);
    }
    
    private function createTangle(ObjectManager $manager) {
        $tangle = new Tangle();
        $tangle->setName('Tangle');
        $tangle->setDescription('Sample tangle');
        
        $manager->persist($tangle);
        $this->addReference('tangle', $tangle);
    }
    
    private function createUserTangle(ObjectManager $manager, $userReference, $isOwner, $credit) {
        $userTangle = new UserTangle();
        $userTangle->setUser($this->getReference("$userReference"));
        $userTangle->setTangle($this->getReference('tangle'));
        $userTangle->setTangleOwner($isOwner);
        $userTangle->setCredit($credit);
        
        $manager->persist($userTangle);
        $this->addReference('userTangle_' . "$userReference", $userTangle);
    }
    
    private function createRequest(ObjectManager $manager, $userReference, $description, $requestNumber) {
        $request = new Request();
        $request->setUser($this->getReference("$userReference"));
        $request->setTangle($this->getReference('tangle'));
        $request->setDescription("$description");
        
        $manager->persist($request);
        $this->addReference('request' . "$requestNumber", $request);
    }
    
    private function createOffer(ObjectManager $manager, $userReference, $description, $offerNumber, $requestReference) {
        $offer = new Offer();
        $offer->setUser($this->getReference("$userReference"));
        $offer->setRequest($this->getReference("$requestReference"));
        $offer->setDescription($description);
        $offer->setRequestedPrice(25);
        
        $manager->persist($offer);
        $this->addReference('offer' . "$offerNumber", $offer);
    }
}