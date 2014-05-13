<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
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
     * A function called at the beginning of every test
     * Overriden to add needed fixtures
     */
    public function setup() {  
        parent::setup();
        $loader = new Loader();
        $loader->addFixture(new LoadTangleData());
        $loader->addFixture(new LoadUserData());
        $loader->addFixture(new LoadUserTangleData());
        $loader->addFixture(new LoadSessionData());
 
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }
    
    /*
     * Test Case testing sending a wrong session to AllUsersAction
     */
    public function testAllUsersAction_WrongSession(){
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
     * 
     * @author HebaAamer 
     */
    public function testLeaveTangleAction_NullTangleId() {
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle//user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID'=>'session'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    /**
     * Test Case testing not sending a session id in the 
     */
    public function testLeaveTangleAction_NullSessionId() {
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
     */
    public function testLeaveTangleAction_ExpiredSession() {
        $client = static::createClient();
        $client->request('DELELTE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID', 'sampleSession'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
}

