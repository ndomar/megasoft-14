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
        $this->makeUsers($manager);
        $this->makeSessions($manager);
        $this->makeTangles($manager);
        $this->makeUserTangles($manager);
        $this->makeRequests($manager);
        $this->makeOffers($manager);
        $this->makeMessages($manager);
        $this->makeTransactions($manager);
        $this->makeClaims($manager);
                
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
    
    private function createUserEmail(ObjectManager $manager, $userReference) {
        $userEmail = new UserEmail();
        $userEmail->setUser($this->getReference("$userReference"));
        $userEmail->setEmail("$userReference" . '@entangle.io');
        
        $manager->persist($userEmail);
    }
    
    private function createTangle(ObjectManager $manager) {
        $tangle = new Tangle();
        $tangle->setName('Tangle');
        $tangle->setDescription('Sample tangle');
        
        $manager->persist($tangle);
        $this->addReference('tangle', $tangle);
    }
    
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
    
    private function createRequest(ObjectManager $manager, $userReference, $description, $requestNumber, $requestStatus) {
        $request = new Request();
        $request->setUser($this->getReference("$userReference"));
        $request->setTangle($this->getReference('tangle'));
        $request->setDescription("$description");
        $request->setStatus($requestStatus);
        
        $manager->persist($request);
        $this->addReference('request' . "$requestNumber", $request);
    }
    
    private function createOffer(ObjectManager $manager, $userReference, $description, $offerNumber, $requestReference, $offerStatus) {
        $offer = new Offer();
        $offer->setUser($this->getReference("$userReference"));
        $offer->setRequest($this->getReference("$requestReference"));
        $offer->setDescription($description);
        $offer->setRequestedPrice(25);
        $offer->setStatus($offerStatus);
        
        $manager->persist($offer);
        $this->addReference('offer' . "$offerNumber", $offer);
    }
    
    private function createTransaction(ObjectManager $manager, $offerReference) {
        $transaction = new Transaction();
        $transaction->setOffer($this->getReference("$offerReference"));
        $transaction->setDate(new DateTime('now'));
        $transaction->setFinalPrice(30);
        
        $manager->persist($transaction);
        $this->addReference("$offerReference" . 'Transaction', $transaction);
    }
    
    private function createClaim(ObjectManager $manager, $offerReference, $userReference, $claimNumber) {
        $claim = new Claim();
        $claim->setClaimer($this->getReference("$userReference"));
        $claim->setTangle('tangle');
        $claim->setOffer($this->getReference("$offerReference"));
        $claim->setCreated(new DateTime('now'));
        
        $manager->persist($claim);
        $this->addReference('claim' . "$claimNumber", $claim);
    }
    
    private function createMessage(ObjectManager $manager, $messageBody, $userReference, $offerReference, $messageNumber){
        $message = new Message();
        $message->setOffer($this->getReference("$offerReference"));
        $message->setSender($this->getReference("$userReference"));
        $message->setDate(new DateTime('now'));
        $message->setBody($messageBody);
        
        $manager->persist($message);
        $this->addReference('message' . "$messageNumber", $message);
    }
    
    private function makeUsers(ObjectManager $manager){
        $this->createUser($manager, 'Ahmad', 'ahmad');
        $this->createUser($manager, 'Mohamed', 'mohamed');
        $this->createUser($manager, 'Aly', 'aly');
        $this->createUser($manager, 'Mazen', 'mazen');
        $this->createUser($manager, 'Adel', 'adel');
        
        $this->createUserEmail($manager, 'userAhmad');
        $this->createUserEmail($manager, 'userMohamed');
        $this->createUserEmail($manager, 'userAly');
        $this->createUserEmail($manager, 'userMazen');
        $this->createUserEmail($manager, 'userAdel');
    }
    
    private function makeSessions(ObjectManager $manager){
        $this->createSession($manager, 'userAhmad', 'userAhmad', false, '1');
        $this->createSession($manager, 'userMohamed', 'userMohamed', true, '2');
        $this->createSession($manager, 'userAly', 'userAly', false, '3');
        $this->createSession($manager, 'userMazen', 'userMazen', false, '4');
        $this->createSession($manager, 'userAdel', 'userAdel', false, '5');
    }
    
    private function makeTangles(ObjectManager $manager){
        $this->createTangle($manager);
    }
    
    private function makeUserTangles(ObjectManager $manager){
        $this->createUserTangle($manager, 'userAhmad', true, 0, false);
        $this->createUserTangle($manager, 'userMohamed', false, 80, false);
        $this->createUserTangle($manager, 'userAly', false, -80, false);
        $this->createUserTangle($manager, 'userAdel', false, 0, true);
    }
    
    private function makeRequests(ObjectManager $manager){
        $this->createRequest($manager, 'userMohamed', 'i want to buy a car', 1, 1);
        $this->createRequest($manager, 'userMohamed', 'i want to travel to London', 2, 0);
        $this->createRequest($manager, 'userMohamed', 'i want to go to the doctor', 3, 2);
        $this->createRequest($manager, 'userAly', 'i want to buy a book', 4, 0);
        $this->createRequest($manager, 'userAly', 'i want to have a reminder software', 5, 2);
        $this->createRequest($manager, 'userAly', 'i want to have a ride tomorrow', 6, 1);
    }
    
    private function makeOffers(ObjectManager $manager){
        $this->createOffer($manager, 'userAly', 'i can help', 1, 'request1', 1);
        $this->createOffer($manager, 'userAly', 'i want to help', 2, 'request2', 0);
        $this->createOffer($manager, 'userAly', 'i can do this for you', 3, 'request3', 2);
        $this->createOffer($manager, 'userMohamed', 'i will help you', 4, 'request4', 3);
        $this->createOffer($manager, 'userMohamed', 'i can help you', 5, 'request5', 2);
        $this->createOffer($manager, 'userMohamed', 'this is easy', 6, 'request6', 1);
    }
    
    private function makeMessages(ObjectManager $manager){
        $this->createMessage($manager, "hi1", 'userMohamed', 'offer1', 1);
        $this->createMessage($manager, "hi2", 'userAly', 'offer1', 2);
        $this->createMessage($manager, "hi3", 'userMohamed', 'offer1', 3);
        $this->createMessage($manager, "hi4", 'userAly', 'offer2', 4);
        $this->createMessage($manager, "hi5", 'userAly', 'offer2', 5);
    }
    
    private function makeTransactions(ObjectManager $manager){
        $this->createTransaction($manager, 'offer1');
        $this->createTransaction($manager, 'offer6');
    }
    
    private function makeClaims(ObjectManager $manager){
        $this->createClaim($manager, 'offer3', 'userAly', 1);
        $this->createClaim($manager, 'offer3', 'userMohamed', 2);
        $this->createClaim($manager, 'offer5', 'userAly', 3);         
    }
}