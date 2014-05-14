<?php
/**
 * Created by PhpStorm.
 * User: almgohar
 * Date: 5/14/14
 * Time: 5:54 PM
 */

namespace Megasoft\EntangleBundle\Tests\Controller;


use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

class UserControllerTest extends EntangleTestCase {

    public function testGeneralProfileAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession2'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Wrong SessionId");
    }

    public function testGeneralProfileAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    public function testGeneralProfileAction_NoSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>''));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking empty sessionId");
    }

    public function testGeneralProfileAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/2/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'checking unauthorized user');

    }

    public function testGeneralProfileAction_GetGeneralProfile() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('name',$json,true, 'The user name is not found');
        $this->assertArrayHasKey('description',$json,true, 'The user description is not fount');
        $this->assertArrayHasKey('photo',$json,true, 'The user photo is not fount');
        $this->assertArrayHasKey('verified',$json,true, 'The user verification is not fount');
        $this->assertEquals('sampleUser',$json['name']);
    }


    public function testProfileAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    public function testProfileAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession4'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking wrong sessionId");
    }

    public function testProfileAction_EmptySession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>''));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking empty sessionId");
    }

    public function testProfileAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession3'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking unauthorized user");
    }

    public function testProfileAction_NotFoundTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/3/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking not found tangle");
    }

    public function testProfileAction_WrongTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/2/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking wrong tangle");
    }

    public function testProfileAction_UserNotInTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/2/user/3/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking logged in user not in tangle");
    }

    public function testProfileAction_UserNotFound() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/5/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking required user not found");
    }

    public function testProfileAction_GetProfile() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/2/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('name',$json,true, 'The user name is not found');
        $this->assertArrayHasKey('description',$json,true, 'The user description is not fount');
        $this->assertArrayHasKey('photo',$json,true, 'The user photo is not fount');
        $this->assertArrayHasKey('verified',$json,true, 'The user verification is not fount');
        $this->assertEquals('sampleUser1',$json['name']);
    }
    
}
 