<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
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
     public function testResetTangleAction_NoSessionId() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset');

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'check for having no session id');
    }

    public function testResetAction_BadSessionId() {
        $this->addFixture(new LoadResetTangletData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'badSessionId'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for having bad session id');
    }

    public function testResetTangleAction_ExpiredSessionId() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userMohamed'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for expired session id');
    }

    public function testResetTangleAction_NoSuchTangle() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/-1/reset');

        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Check for tangle doesn\'t exist');
    }

    public function testResetTangleAction_UserNotOwner() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for user not owner');
    }

    public function testResetTangleAction_UserNotInTheTangle() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userMazen'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'Check for user not in the tangle');
    }

    public function testResetTangleAction_RequestsDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmed'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $requests = $tangle->getRequests();
        foreach ($requests as $request) {
            $deleted = $request->getDeleted();
            $this->assertEquals(1, $deleted, 'Check all requests are deleted');
        }
    }

    public function testResetTangleAction_OffersDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmed'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $requests = $tangle->getRequests();
        $offers = $requests->getOffers();
        foreach ($offers as $offer) {
            $deleted = $offer->getDeleted();
            $this->assertEquals(1, $deleted, 'Check all offers are deleted');
        }
    }

    public function testResetTangleAction_ClaimsDeleted() {
        $this->addFixture(new LoadResetTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('PUT', '/tangle/1/reset', array(), array(), array('HTTP_X_SESSION_ID' => 'userAhmed'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $tangleRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Tangle");
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        $claims = $tangle->getClaims();
        foreach ($claims as $claim) {
            $deleted = $claim->getDeleted();
            $this->assertEquals(1, $deleted, 'Check all claims are deleted');
        }
    }
    

}
