<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\Claim;
use Megasoft\EntangleBundle\Entity\Session;
use DateTime;

class LoadClaimData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
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
    public function getOrder() {
        return 1;
    }

    /**
     * This function is used to create a user
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $name
     * @param String $password
     * @author Salma Amr
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
     * @author Salma Amr
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
     * This function is used to create a userEmail
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @author Salma Amr
     */
    private function createUserEmail(ObjectManager $manager, $userReference) {
        $userEmail = new UserEmail();
        $userEmail->setUser($this->getReference("$userReference"));
        $userEmail->setEmail("$userReference" . '@entangle.io');

        $manager->persist($userEmail);
    }

    /**
     * This function is used to create a tangle
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
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
     * @author Salma Amr
     */
    private function createUserTangle(ObjectManager $manager, $userReference, $isOwner, $credit) {
        $userTangle = new UserTangle();
        $userTangle->setUser($this->getReference("$userReference"));
        $userTangle->setTangle($this->getReference('tangle'));
        $userTangle->setTangleOwner($isOwner);
        $userTangle->setCredit($credit);

        $manager->persist($userTangle);
        $this->addReference('userTangle' . "$userReference", $userTangle);
    }

    /**
     * This function is used to create a request
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @param String $description
     * @param integer $requestNumber
     * @param integer $requestStatus
     * @author Salma Amr
     */
    private function createRequest(ObjectManager $manager, $userReference, $description, $requestNumber, $requestStatus) {
        $request = new Request();
        $request->setUser($this->getReference("$userReference"));
        $request->setTangle($this->getReference('tangle'));
        $request->setDescription("$description");
        $request->setStatus($requestStatus);
        $request->setDate(new DateTime('now'));

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
     * @author Salma Amr
     */
    private function createOffer(ObjectManager $manager, $userReference, $description, $offerNumber, $requestReference, $offerStatus, $deleted) {
        $offer = new Offer();
        $offer->setUser($this->getReference("$userReference"));
        $offer->setRequest($this->getReference("$requestReference"));
        $offer->setDescription($description);
        $offer->setRequestedPrice(25);
        $offer->setStatus($offerStatus);
        $offer->setDeleted($deleted);
        $offer->setDate(new DateTime('now'));

        $manager->persist($offer);
        $this->addReference('offer' . "$offerNumber", $offer);
    }

    /**
     * This function is used to create a claim
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param Strimg $offerReference
     * @param String $userReference
     * @param integer $claimNumber
     * @author Salma Amr
     */
    private function createClaim(ObjectManager $manager, $offerReference, $userReference, $claimNumber) {
        $claim = new Claim();
        $claim->setClaimer($this->getReference("$userReference"));
        $claim->setTangle($this->getReference('tangle'));
        $claim->setOffer($this->getReference("$offerReference"));
        $claim->setCreated(new DateTime('now'));
        $claim->setMessage('i am claiming');
        $claim->setStatus(0);

        $manager->persist($claim);
        $this->addReference('claim' . "$claimNumber", $claim);
    }

    /**
     * This function is used to make testing users and their userEmails
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeUsers(ObjectManager $manager) {
        $this->createUser($manager, 'Aly', 'aly');
        $this->createUser($manager, 'Mazen', 'mazen');
        $this->createUser($manager, 'Mohamed', 'mohamed');
        $this->createUser($manager, 'Ahmad', 'ahmad');
        $this->createUser($manager, 'Fahmy', 'fahmy');

        $this->createUserEmail($manager, 'userAly');
        $this->createUserEmail($manager, 'userMazen');
        $this->createUserEmail($manager, 'userMohamed');
    }

    /**
     * This function is used to make testing sessions
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeSessions(ObjectManager $manager) {

        $this->createSession($manager, 'userAly', 'userAly', false, '3');
        $this->createSession($manager, 'userMohamed', 'userMohamed', true, '2');
        $this->createSession($manager, 'userMazen', 'userMazen', false, '4');
        $this->createSession($manager, 'userFahmy', 'userFahmy', false, '5');
    }

    /**
     * This function is used to make a testing tangle
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeTangles(ObjectManager $manager) {
        $this->createTangle($manager);
    }

    /**
     * This function is used to make testing userTangles
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeUserTangles(ObjectManager $manager) {
        $this->createUserTangle($manager, 'userMohamed', false, 80);
        $this->createUserTangle($manager, 'userAly', false, -80);
        $this->createUserTangle($manager, 'userMazen', true, -80);
        $this->createUserTangle($manager, 'userFahmy', false, -80);
    }

    /**
     * This function is used to make testing requests
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeRequests(ObjectManager $manager) {
        $this->createRequest($manager, 'userMohamed', 'i want to buy a car', 1, 1);
        $this->createRequest($manager, 'userMohamed', 'i want to travel to London', 2, 0);
        $this->createRequest($manager, 'userMohamed', 'i want to go to the doctor', 3, 2);
        $this->createRequest($manager, 'userAly', 'i want to buy a book', 4, 0);
        $this->createRequest($manager, 'userAly', 'i want to have a reminder software', 5, 2);
        $this->createRequest($manager, 'userAly', 'i want to have a ride tomorrow', 6, 1);
    }

    /**
     * This function is used to make testing offers
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeOffers(ObjectManager $manager) {
        $this->createOffer($manager, 'userAly', 'i can help', 1, 'request1', 1, false);
        $this->createOffer($manager, 'userAly', 'i want to help', 2, 'request2', 3, false);
        $this->createOffer($manager, 'userAly', 'i can do this for you', 3, 'request3', 2, false);
        $this->createOffer($manager, 'userMohamed', 'i will help you', 4, 'request4', 0, false);
        $this->createOffer($manager, 'userMohamed', 'i can help you', 5, 'request5', 2, false);
        $this->createOffer($manager, 'userMohamed', 'this is easy', 6, 'request6', 1, true);
        $this->createOffer($manager, 'userFahmy', 'this is easy', 7, 'request1', 2, false);
    }

    /**
     * This function is used to make testing claims
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @author Salma Amr
     */
    private function makeClaims(ObjectManager $manager) {
        $this->createClaim($manager, 'offer3', 'userAly', 1);
        $this->createClaim($manager, 'offer3', 'userMohamed', 2);
        $this->createClaim($manager, 'offer6', 'userAhmad', 4);
        $this->createClaim($manager, 'offer5', 'userAly', 3);
        $this->createClaim($manager, 'offer7', 'userFahmy', 5);
    }

}
