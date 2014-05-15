<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadResetTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

/*
 * Test Class for Tangle Controller
 * @author OmarElAzazy
 */
class TangleControllerTest extends EntangleTestCase
{
    
    /*
     * Test Case testing sending a wrong session to AllUsersAction
     * @author OmarElAzazy
     */
    public function testAllUsersAction_WrongSession(){
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET', 
                '/tangle/1/user', 
                array(), 
                array(), 
                array('HTTP_X_SESSION_ID'=>'wrongSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    /*
     * Test Case testing sending correct request to AllUsersAction
     * @author OmarElAzazy
     */
    public function testAllUsersAction_GetListWithSampleUser(){
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET', 
                '/tangle/1/user', 
                array(), 
                array(), 
                array('HTTP_X_SESSION_ID'=>'sampleSession'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $json_string = $client->getResponse()->getContent();
        $this->assertJson($json_string);
        
        $json = json_decode($json_string, true);
        $this->assertEquals(2, sizeof($json));
        $this->assertEquals(true, isset($json['count']));
        $this->assertEquals(true, isset($json['users']));
        $this->assertEquals(1, $json['count']);
        
        $users = $json['users'];
        $this->assertEquals(1, $users[0]['id']);
        $this->assertEquals('sampleUser', $users[0]['username']);
        $this->assertEquals(0, $users[0]['balance']);
        $this->assertEquals('http://entangle.io/images/profilePictures/', $users[0]['iconUrl']);
    }
    /**
     * Test case for having no session id to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
     public function testResetTangleAction_NoSessionId() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset');

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'check for having no session id');
    }
    /**
     * Test case for having bad session id to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetAction_BadSessionId() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'badSessionId'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for having bad session id');
    }
    /**
     * Test case for having expired session id to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_ExpiredSessionId() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userMohamed'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for expired session id');
    }
    /**
     * Test case for invalid tangle id to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_NoSuchTangle() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/-1/reset');

        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Check for tangle doesn\'t exist');
    }
    /**
     * Test case for the user not being the owner to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_UserNotOwner() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for user not owner');
    }
    /**
     * Test case for the user not being in the tangle to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_UserNotInTheTangle() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userMazen'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for user not in the tangle');
    }
    /**
     * Test case to make sure requests are deleted to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_RequestsDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $requests = $tangle->getRequests();
        foreach ($requests as $request) {
            $deleted = $request->getDeleted();
            $this->assertEquals(1, $deleted, 'Check all requests are deleted');
        }
    }
    /**
     * Test case to make sure offers are deleted to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_OffersDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $requests = $tangle->getRequests();
        foreach ($requests as $request) {
            $offers = $request->getOffers();
            foreach ($offers as $offer) {
                $deleted = $offer->getDeleted();
                $this->assertEquals(1, $deleted, 'Check all offers are deleted');
            }
        }
    }
    /**
     * Test case to make sure claims are deleted to resetTangle action to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_ClaimsDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $claims = $tangle->getClaims();
        foreach ($claims as $claim) {
            $deleted = $claim->getDeleted();
            $this->assertEquals(1, $deleted, 'Check all claims are deleted');
        }
    }
    /**
     * Test case to make sure credit is set to zero to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_CreditIsZero() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $userTangles = $tangle->getUserTangles();
        foreach ($userTangles as $userTangle) {
            $credit = $userTangle->getCredit();
            $this->assertEquals(0, $credit, 'Check all credits are set to zero');
        }
    }
    /**
     * Test case to make sure transactions are deleted to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_TransactionsDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $requests = $tangle->getRequests();
        foreach ($requests as $request) {
            $offers = $request->getOffers();
            foreach ($offers as $offer) {
                $offerId = $offer->getId();
                $transactionRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Transaction");
                $transaction = $transactionRepo->findOneBy(array('offerId' => $offerId));
                if ($transaction != null) {
                    $deleted = $transaction->getDeleted();
                }
                $this->assertEquals(1, $deleted, 'Check all transactions are deleted');
            }
        }
    }
    /**
     * Test case to make sure messages are deleted to resetTangle action
     * @param none
     * @return none
     * @author Salma Khaled
     */
    public function testResetTangleAction_MessagesDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $requests = $tangle->getRequests();
        foreach ($requests as $request) {
            $offers = $request->getOffers();
            foreach ($offers as $offer) {
                $offerId = $offer->getId();
                $messagesRepo = $this->doctrine->getRepository("MegasoftEntangleBundle:Message");
                $messages = $messagesRepo->findBy(array('offerId' => $offerId));
                foreach ($messages as $message) {
                    $deleted = $message->getDeleted();
                    $this->assertEquals(1, $deleted, 'Check all messages are deleted');
                }
            }
        }
    }

}
