<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

/*
 * Test Class for Tangle Controller
 */
class TangleControllerTest extends EntangleTestCase
{
    
    /*
     * Test Case testing sending a wrong session to AllUsersAction
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
}
