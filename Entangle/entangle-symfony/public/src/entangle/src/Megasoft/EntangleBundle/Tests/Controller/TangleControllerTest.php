<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadCreateTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

/*
 * Test Class for Tangle Controller
 * @author OmarElAzazy
 */

class TangleControllerTest extends EntangleTestCase {
    /*
     * Test Case testing sending a wrong session to AllUsersAction
     * @author OmarElAzazy
     */

    public function testAllUsersAction_WrongSession() {
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', '/tangle/1/user', array(), array(), array('HTTP_X_SESSION_ID' => 'wrongSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /*
     * Test Case testing sending correct request to AllUsersAction
     * @author OmarElAzazy
     */

    public function testAllUsersAction_GetListWithSampleUser() {
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', '/tangle/1/user', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

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

    public function testCreateTangleAction_WrongSession() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'wrongCreateTangleTestSession'), $jsonBody);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Wrong Session");
    }

    public function testCreateTangleAction_EmptySession() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array(), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Session");
    }

    public function testCreateTangleAction_EmptyIcon() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
                array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Icon");
    }

    public function testCreateTangleAction_EmptyName() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
                array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Name");
    }

    public function testCreateTangleAction_EmptyDescription() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'testTangle',
            'tangleIcon' => '1');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
                array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Description");
    }

    public function testCreateTangleAction_ExistingName() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'testTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
            array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Tangle already exists");
    }

    public function testCreateTangleAction_TangleCreation() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(201, $client->getResponse()->getStatusCode(), "Error Creating Tangle");
    }

    public function testCreateTangleAction_NullName() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => Null, 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Tangle Name");
    }

    public function testCreateTangleAction_NullIcon() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => Null,
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Tangle Icon");
    }

    public function testCreateTangleAction_NullDescription() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => Null);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Tangle Description");
    }

    public function testCreateTangleAction_NullSession() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => Null), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Session");
    }
}
