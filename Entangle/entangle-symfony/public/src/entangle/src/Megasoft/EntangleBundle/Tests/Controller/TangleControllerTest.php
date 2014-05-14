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
    
    /**
     * Test Case testing not sending a tangle id in the request
     * @author HebaAamer 
     */
    public function testLeaveTangleAction_NullTangleId() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle//user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID'=>'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    /**
     * Test Case testing not sending a session id in the 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_NullSessionId() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', null));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    /**
     * Test Case testing sending expired session 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_ExpiredSession() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    /**
     * Test Case testing sending wrong session 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_WrongSession() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', 'session'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    /**
     * Test Case testing user not in the tangle 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_NotUserInTangle() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', 'userMazen'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
    }
    
    /**
     * Test Case to check the condition of being a tangle owner
     * @author HebaAamer
     */
    public function testLeaveTangleAction_IsTangleOwner() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', 'userAhmad'));
        $response = $client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }
    
    public function testLeaveTangleAction_LeftUser() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', 'userAhmad'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode());
    }
}

