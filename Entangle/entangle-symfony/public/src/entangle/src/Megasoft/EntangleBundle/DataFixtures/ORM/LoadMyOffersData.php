<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;

/*
 * Fixtures for view my offers end-point
 */
class LoadMyOffersData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->makeUsers($manager);
        $this->makeSessions($manager);
        $this->makeTangles($manager);
        $this->makeUserTangles($manager);
        $this->makeRequests($manager);
        $this->makeOffers($manager);                
        
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
    
    /**
     * This function is used to create a user
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $name
     * @param String $password
     * @author HebaAamer
     */
    private function createUser(ObjectManager $manager, $name, $password) {
        $user = new User();
        $user->setName($name);
        $user->setPassword($password);
        
        $manager->persist($user);
        $this->addReference('user' . "$name", $user);
    }
    
    /**
     * This function is used to create a session
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @param String $sessionId
     * @param boolean $expired
     * @param String $regId
     * @author HebaAamer
     */
    private function createSession(ObjectManager $manager, $userReference, $sessionId, $expired, $regId) {
        $session = new Session();
        $session->setUser($this->getReference("$userReference"));
        $session->setSessionId("$sessionId");
        $session->setExpired($expired);
        $session->setCreated(new DateTime('now'));
        $session->setDeviceType('Samsung S4');
        $session->setRegId("$regId");
        
        $manager->persist($session);
        $this->addReference('session_' . "$userReference", $session);
    }
    
    /**
     * This function is used to create a tangle
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function createTangle(ObjectManager $manager) {
        $tangle = new Tangle();
        $tangle->setName('Tangle');
        $tangle->setDescription('Sample tangle');
        
        $manager->persist($tangle);
        $this->addReference('tangle', $tangle);
    }
    
    /**
     * This function is used to create a userTangle
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @param boolean $isOwner
     * @param integer $credit
     * @param boolean $left
     * @author HebaAamer
     */
    private function createUserTangle(ObjectManager $manager, $userReference, $isOwner, $credit, $left) {
        $userTangle = new UserTangle();
        $userTangle->setUser($this->getReference("$userReference"));
        $userTangle->setTangle($this->getReference('tangle'));
        $userTangle->setTangleOwner($isOwner);
        $userTangle->setCredit($credit);
        if($left){
            $userTangle->setLeavingDate(new DateTime('now'));
        }
        $manager->persist($userTangle);
        $this->addReference('userTangle_' . "$userReference", $userTangle);
    }
    
    /**
     * This function is used to create a request
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @param String $description
     * @param integer $requestNumber
     * @param integer $requestStatus
     * @author HebaAamer
     */
    private function createRequest(ObjectManager $manager, $userReference, $description, $requestNumber, $requestStatus, $deleted) {
        $request = new Request();
        $request->setUser($this->getReference("$userReference"));
        $request->setTangle($this->getReference('tangle'));
        $request->setDescription("$description");
        $request->setStatus($requestStatus);
        $request->setDate(new DateTime('now'));
        $request->setDeleted($deleted);
        
        $manager->persist($request);
        $this->addReference('request' . "$requestNumber", $request);
    }
    
    /**
     * This function is used to create an offer
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @param String $description
     * @param integer $offerNumber
     * @param String $requestReference
     * @param integer $offerStatus
     * @author HebaAamer
     */
    private function createOffer(ObjectManager $manager, $userReference, $description, $offerNumber, $requestReference, $offerStatus, $deleted) {
        $offer = new Offer();
        $offer->setUser($this->getReference("$userReference"));
        $offer->setRequest($this->getReference("$requestReference"));
        $offer->setDescription($description);
        $offer->setRequestedPrice(25);
        $offer->setStatus($offerStatus);
        $offer->setDate(new DateTime('now'));
        $offer->setDeleted($deleted);
        
        $manager->persist($offer);
        $this->addReference('offer' . "$offerNumber", $offer);
    }
    
    /**
     * This function is used to make testing users and their userEmails
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function makeUsers(ObjectManager $manager){
        $this->createUser($manager, 'Ahmad', 'ahmad');
        $this->createUser($manager, 'Mohamed', 'mohamed');
        $this->createUser($manager, 'Aly', 'aly');
        $this->createUser($manager, 'Mazen', 'mazen');
        $this->createUser($manager, 'Adel', 'adel');
    }
    
    /**
     * This function is used to make testing sessions
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function makeSessions(ObjectManager $manager){
        $this->createSession($manager, 'userAhmad', 'userAhmad', false, '1');
        $this->createSession($manager, 'userMohamed', 'userMohamed', true, '2');
        $this->createSession($manager, 'userAly', 'userAly', false, '3');
        $this->createSession($manager, 'userMazen', 'userMazen', false, '4');
        $this->createSession($manager, 'userAdel', 'userAdel', false, '5');
    }
    
    /**
     * This function is used to make a testing tangle
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function makeTangles(ObjectManager $manager){
        $this->createTangle($manager);
    }
    
    /**
     * This function is used to make testing userTangles
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function makeUserTangles(ObjectManager $manager){
        $this->createUserTangle($manager, 'userAhmad', true, 0, false);
        $this->createUserTangle($manager, 'userMohamed', false, 80, false);
        $this->createUserTangle($manager, 'userAly', false, -80, false);
        $this->createUserTangle($manager, 'userAdel', false, 0, true);
    }
    
    /**
     * This function is used to make testing requests
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function makeRequests(ObjectManager $manager){
        $this->createRequest($manager, 'userMohamed', 'i want to buy a car', 1, 1, true);
        $this->createRequest($manager, 'userMohamed', 'i want to travel to London', 2, 0, false);
        $this->createRequest($manager, 'userMohamed', 'i want to go to the doctor', 3, 2, false);
        $this->createRequest($manager, 'userMohamed', 'i want to buy a book', 4, 0, false);
        $this->createRequest($manager, 'userMohamed', 'i want to have a reminder software', 5, 2, true);
        $this->createRequest($manager, 'userMohamed', 'i want to have a ride tomorrow', 6, 1, false);
    }
    
    /**
     * This function is used to make testing offers
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author HebaAamer
     */
    private function makeOffers(ObjectManager $manager){
        $this->createOffer($manager, 'userAly', 'i can help', 1, 'request1', 1, true);
        $this->createOffer($manager, 'userAly', 'i want to help', 2, 'request2', 0, false);
        $this->createOffer($manager, 'userAly', 'i can do this for you', 3, 'request3', 2, false);
        $this->createOffer($manager, 'userAly', 'i will help you', 4, 'request4', 3, false);
        $this->createOffer($manager, 'userAly', 'i can help you', 5, 'request5', 2, true);
        $this->createOffer($manager, 'userAly', 'this is easy', 6, 'request6', 1, false);
    }
}