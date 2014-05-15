<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadMyRequestsData;
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
     * Test Case sending wrong tangle id in the request
     * @author HebaAamer 
     */
    public function testUserRequestsAction_WrongTangleId() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/3/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode(), 'Wrong tangle id');
    }
    
    /**
     * Test Case testing not sending a session id in the 
     * @author HebaAamer
     */
    public function testUserRequestsAction_NullSessionId() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Not sending session id');
    }
    
    /**
     * Test Case testing sending expired session 
     * @author HebaAamer
     */
    public function testUserRequestsAction_ExpiredSession() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Sending expired session');
    }
    
    /**
     * Test Case testing sending wrong session 
     * @author HebaAamer
     */
    public function testUserRequestsAction_WrongSession() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'mohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Sending wrong session');
    }
    
    /**
     * Test Case testing user not in the tangle 
     * @author HebaAamer
     */
    public function testUserRequestsAction_NotUserInTangle() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userMazen'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'Case user not in the tangle');
    }
    
    /**
     * Test Case to check if the user left the tangle or not
     * @author HebaAamer
     */
    public function testUserRequestsAction_LeftUser() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAdel'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'Case left user');
    }
    
    /**
     * Test Case to get the requests of the tangle owner
     * @author HebaAamer
     */
    public function testUserRequestsAction_CaseTangleOwner() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'Case Tangle Owner');
        $content = $response->getContent();
        $this->assertJson($content, 'Output JSON is wrong formated');
        
        $json = json_decode($content);
        $this->assertArrayHasKey('count',$json, 'count not found in response');
        $this->assertArrayHasKey('requests', 'requests not found in response');
        
        $this->assertEquals(0, $json['count']);
        $this->assertNull($json['requests']);
    }
    
    /**
     * Test Case to get the requests of the tangle member
     * @author HebaAamer
     */
    public function testUserRequestsAction_CaseTangleMember() {
        $this->addFixture(new LoadMyRequestsData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('GET',
                'tangle/1/user/requests',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'Case Tangle Member');
        $content = $response->getContent();
        $this->assertJson($content, 'Output JSON is wrong formated');
        
        $json = json_decode($content);
        $this->assertArrayHasKey('count',$json, 'count not found in response');
        $this->assertArrayHasKey('requests', 'requests not found in response');
        
        //$this->assertEquals(0, $json['count']);
        //$this->assertNull($json['requests']);
    }
}
