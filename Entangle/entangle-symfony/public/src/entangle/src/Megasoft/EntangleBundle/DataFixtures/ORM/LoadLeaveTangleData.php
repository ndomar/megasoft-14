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
use Megasoft\EntangleBundle\Entity\Message;
use Megasoft\EntangleBundle\Entity\Transaction;
use DateTime;


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
     * This function is used to create a userEmail
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @param String $userReference
     * @author HebaAamer
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
     * @author HebaAamer
     */
    private function createTangle(ObjectManager $manager) {
        $tangle = new Tangle();
        $tangle->setName('Tangle');
        $tangle->setDescription('Sample tangle');
        
        $manager->persist($tangle);
        $this->addReference('tangle', $tangle);
    }
    
    
}