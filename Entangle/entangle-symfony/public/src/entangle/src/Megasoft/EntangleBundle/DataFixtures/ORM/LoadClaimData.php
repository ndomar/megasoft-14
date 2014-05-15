<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class LoadClaimData extends AbstractFixture implements OrderedFixtureInterface
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
    
    private function createUserTangle(ObjectManager $manager, $userReference, $isOwner, $credit) {
        $userTangle = new UserTangle();
        $userTangle->setUser($this->getReference("$userReference"));
        $userTangle->setTangle($this->getReference('tangle'));
        $userTangle->setTangleOwner($isOwner);
        $userTangle->setCredit($credit);
        
        $manager->persist($userTangle);
        $this->addReference('userTangle' . "$userReference", $userTangle);
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
    
       private function createOffer(ObjectManager $manager, $userReference, $description, $offerNumber, $requestReference, $offerStatus, $deleted) {
        $offer = new Offer();
        $offer->setUser($this->getReference("$userReference"));
        $offer->setRequest($this->getReference("$requestReference"));
        $offer->setDescription($description);
        $offer->setRequestedPrice(25);
        $offer->setStatus($offerStatus);
        $offer->setDeleted($deleted);
        
        $manager->persist($offer);
        $this->addReference('offer' . "$offerNumber", $offer);
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
      private function makeUsers(ObjectManager $manager){
        $this->createUser($manager, 'Aly', 'aly');
        $this->createUser($manager, 'Mazen', 'mazen');
        
        $this->createUserEmail($manager, 'userAly');
        $this->createUserEmail($manager, 'userMazen');
    }
    
    private function makeSessions(ObjectManager $manager){
        
        $this->createSession($manager, 'userAly', 'userAly', false, '3');
        $this->createSession($manager, 'userMohamed', 'userMohamed', true, '2');
        $this->createSession($manager, 'userMazen', 'userMazen', false, '4');
    }
    
    private function makeTangles(ObjectManager $manager){
        $this->createTangle($manager);
    }
    
    private function makeUserTangles(ObjectManager $manager){
        $this->createUserTangle($manager, 'userMohamed', false, 80);
        $this->createUserTangle($manager, 'userAly', false, -80);
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
        $this->createOffer($manager, 'userAly', 'i can help', 1, 'request1', 1, false);
        $this->createOffer($manager, 'userAly', 'i want to help', 2, 'request2', 3, false);
        $this->createOffer($manager, 'userAly', 'i can do this for you', 3, 'request3', 2, false);
        $this->createOffer($manager, 'userMohamed', 'i will help you', 4, 'request4', 0, false);
        $this->createOffer($manager, 'userMohamed', 'i can help you', 5, 'request5', 2, false);
        $this->createOffer($manager, 'userMohamed', 'this is easy', 6, 'request6', 1, true);
        $this->createOffer($manager, 'userAhmad', 'this is fery easy', 7, 'request6', 2, true);
    }
    
      private function makeClaims(ObjectManager $manager){
        $this->createClaim($manager, 'offer3', 'userAly', 1);
        $this->createClaim($manager, 'offer3', 'userMohamed', 2);
        $this->createClaim($manager, 'offer6', 'userAhmad', 4);
        $this->createClaim($manager, 'offer5', 'userAly', 3);         
    }
}
