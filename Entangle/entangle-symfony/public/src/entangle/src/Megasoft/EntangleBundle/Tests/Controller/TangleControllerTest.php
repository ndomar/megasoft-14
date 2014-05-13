<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadFilterStreamData;
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

    public function testFilterStream_MissingSessionId(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request',
            array(),
            array(),
            array());

        $this->assertEquals(400, $client->getResponse()->getStatusCode(),"Checking Missing SessionId");
    }

    public function testFilterStream_WrongSessionId(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'wrongSessionId'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(),"Checking Wrong SessionId");
    }

    public function testFilterStream_ExpiredSessionId(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession3'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    public function testFilterStream_NotMemberInTheTangle(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession2'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Not Member in tangle");
    }

    public function testFilterStream_SelectAll(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(),"Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content,"Wrong Json Format");
        $json = json_decode($content,true);
        $this->assertArrayHasKey('count',$json,"count not found");
        $this->assertArrayHasKey('requests',$json,"requests not found");

        $this->assertEquals(3,$json['count']);

        $this->assertArrayHasKey('id',$json['requests'][0],"request id not found");
        $this->assertArrayHasKey('username',$json['requests'][0],"requester username not found");
        $this->assertArrayHasKey('userId',$json['requests'][0],"requester userId not found");
        $this->assertArrayHasKey('description',$json['requests'][0],"request description not found");
        $this->assertArrayHasKey('offersCount',$json['requests'][0],"request offercount not found");
        $this->assertArrayHasKey('price',$json['requests'][0],"request price not found");

        $this->assertEquals(3,count($json['requests']));

    }

    public function testFilterStream_SelectFiltered(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?query=de',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(),"Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content,"Wrong Json Format");
        $json = json_decode($content,true);
        $this->assertArrayHasKey('count',$json,"count not found");
        $this->assertArrayHasKey('requests',$json,"requests not found");

        $this->assertEquals(2,$json['count'],"Wrong number of results in count");

        $this->assertArrayHasKey('id',$json['requests'][0],"request id not found");
        $this->assertArrayHasKey('username',$json['requests'][0],"requester username not found");
        $this->assertArrayHasKey('userId',$json['requests'][0],"requester userId not found");
        $this->assertArrayHasKey('description',$json['requests'][0],"request description not found");
        $this->assertArrayHasKey('offersCount',$json['requests'][0],"request offercount not found");
        $this->assertArrayHasKey('price',$json['requests'][0],"request price not found");

        $this->assertEquals(2,count($json['requests']),"Wrong number of results in request array");
    }

    public function testFilterStream_FilterNoRequests(){
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?query=z',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(),"Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content,"Wrong Json Format");
        $json = json_decode($content,true);
        $this->assertArrayHasKey('count',$json,"count not found");
        $this->assertArrayHasKey('requests',$json,"requests not found");

        $this->assertEquals(0,$json['count'],"Wrong number of results in count");
        $this->assertEquals(0,count($json['requests']),"Wrong number of results in request array");
    }

}
