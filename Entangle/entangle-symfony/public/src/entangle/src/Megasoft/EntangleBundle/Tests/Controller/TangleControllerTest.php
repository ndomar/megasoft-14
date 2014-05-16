<?php

namespace Megasoft\EntangleBundle\Tests\Controller;


use Megasoft\EntangleBundle\DataFixtures\ORM\LoadCreateTangleData;
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

    public function testAllUsersAction_WrongSession() {
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

    public function testAllUsersAction_GetListWithSampleUser() {
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
     * Testing if the the session exists or not.
     * @author Mansour
     */
    public function testCreateTangleAction_WrongSession() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'wrongCreateTangleTestSession'), $jsonBody);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Wrong Session");
    }

    /**
     * Checks whether the session id is passed in the header.
     * @author Mansour
     */
    public function testCreateTangleAction_EmptySession() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array(), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking empty session");
    }

    /**
     * Checks the tangle icon exists in the request.
     * @author Mansour
     */
    public function testCreateTangleAction_EmptyIcon() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
                array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking empty tangle icon");
    }

    /**
     * Checks if the tangle name exists in the request.
     * @author Mansour
     */
    public function testCreateTangleAction_EmptyName() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
                array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking empty tangle name");
    }

    /**
     * Check if the tangle description exists in the request.
     * @author Mansour
     */
    public function testCreateTangleAction_EmptyDescription() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'testTangle',
            'tangleIcon' => '1',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
                array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking empty tangle description");
    }

    /**
     * Check if the tangle name already exists in the database.
     * @author Mansour
     */
    public function testCreateTangleAction_ExistingName() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'testTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(),
            array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Tangle already exists");
    }

    /**
     * Checks whether the tangle can be created or not.
     * @author Mansour
     */
    public function testCreateTangleAction_TangleCreation() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(201, $client->getResponse()->getStatusCode(), "Error Creating Tangle");
    }

    /**
     * Checks whether the name is null.
     * @author Mansour
     */
    public function testCreateTangleAction_NullName() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => Null, 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Tangle Name");
    }

    /**
     * Checks whether the icon is null.
     * @author Mansour
     */
    public function testCreateTangleAction_NullIcon() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => Null,
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Tangle Icon");
    }

    /**
     * Checks whether the description is null.
     * @author Mansour
     */
    public function testCreateTangleAction_NullDescription() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => Null,);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => 'CreateTangleTestSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Tangle Description");
    }

    /**
     * Checks whether the session is null.
     * @author Mansour
     */
    public function testCreateTangleAction_NullSession() {
        $this->addFixture(new LoadCreateTangleData());
        $this->loadFixtures();
        $body = array('tangleName' => 'CreateTangleTestTangle', 'tangleIcon' => '1',
            'tangleDescription' => 'Test Description',);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/tangle', array(), array(), array('HTTP_X_SESSION_ID' => Null), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Session");
    }

    /*
     * Test Case testing filtering stream if the sessionId is missing.
     * @author MohamedBassem
     */
    public function testFilterStream_MissingSessionId()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=2',
            array(),
            array(),
            array());

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking Missing SessionId");
    }

    /*
     * Test Case testing filtering stream if the sessionId is wrong.
     * @author MohamedBassem
     */
    public function testFilterStream_WrongSessionId()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=2',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'wrongSessionId'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking Wrong SessionId");
    }

    /*
     * Test Case testing filtering stream if the sessionId is expired.
     * @author MohamedBassem
     */
    public function testFilterStream_ExpiredSessionId()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=2',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession3'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking Expired SessionId");
    }

    /*
     * Test Case testing filtering stream if the user is not in the tangle.
     * @author MohamedBassem
     */
    public function testFilterStream_NotMemberInTheTangle()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=2',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession2'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking Not Member in tangle");
    }

    /*
     * Test Case testing filtering stream if the user is not in the tangle.
     * @author MohamedBassem
     */
    public function testFilterStream_NoLimit()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession1'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Checking That the limit exists");
    }

    /*
     * Test Case testing filtering stream if their is not certain search query. The response should return all
     * open requests.
     * @author MohamedBassem
     */
    public function testFilterStream_SelectAll()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=3',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content, "Wrong Json Format");
        $json = json_decode($content, true);
        $this->assertArrayHasKey('count', $json, "count not found");
        $this->assertArrayHasKey('requests', $json, "requests not found");

        $this->assertEquals(3, $json['count']);

        $this->assertArrayHasKey('id', $json['requests'][0], "request id not found");
        $this->assertArrayHasKey('username', $json['requests'][0], "requester username not found");
        $this->assertArrayHasKey('userId', $json['requests'][0], "requester userId not found");
        $this->assertArrayHasKey('description', $json['requests'][0], "request description not found");
        $this->assertArrayHasKey('offersCount', $json['requests'][0], "request offercount not found");
        $this->assertArrayHasKey('price', $json['requests'][0], "request price not found");
        $this->assertArrayHasKey('date', $json['requests'][0], "request date not found");

        $this->assertEquals(3, count($json['requests']));

    }

    /*
     * Test Case testing filtering stream if the a query parameter is added.
     * @author MohamedBassem
     */
    public function testFilterStream_SelectFiltered()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=3&query=de',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content, "Wrong Json Format");
        $json = json_decode($content, true);
        $this->assertArrayHasKey('count', $json, "count not found");
        $this->assertArrayHasKey('requests', $json, "requests not found");

        $this->assertEquals(2, $json['count'], "Wrong number of results in count");

        $this->assertArrayHasKey('id', $json['requests'][0], "request id not found");
        $this->assertArrayHasKey('username', $json['requests'][0], "requester username not found");
        $this->assertArrayHasKey('userId', $json['requests'][0], "requester userId not found");
        $this->assertArrayHasKey('description', $json['requests'][0], "request description not found");
        $this->assertArrayHasKey('offersCount', $json['requests'][0], "request offercount not found");
        $this->assertArrayHasKey('price', $json['requests'][0], "request price not found");
        $this->assertArrayHasKey('date', $json['requests'][0], "request date not found");

        $this->assertEquals(2, count($json['requests']), "Wrong number of results in request array");
    }

    /*
     * Test Case testing filtering stream with a limit
     * @author MohamedBassem
     */
    public function testFilterStream_SelectWithLimit()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content, "Wrong Json Format");
        $json = json_decode($content, true);
        $this->assertArrayHasKey('count', $json, "count not found");
        $this->assertArrayHasKey('requests', $json, "requests not found");

        $this->assertEquals(1, $json['count'], "Wrong number of results in count");

        $this->assertArrayHasKey('id', $json['requests'][0], "request id not found");
        $this->assertArrayHasKey('username', $json['requests'][0], "requester username not found");
        $this->assertArrayHasKey('userId', $json['requests'][0], "requester userId not found");
        $this->assertArrayHasKey('description', $json['requests'][0], "request description not found");
        $this->assertArrayHasKey('offersCount', $json['requests'][0], "request offercount not found");
        $this->assertArrayHasKey('price', $json['requests'][0], "request price not found");
        $this->assertArrayHasKey('date', $json['requests'][0], "request date not found");

        $this->assertEquals(1, count($json['requests']), "Wrong number of results in request array");
    }

    /*
     * Test Case testing filtering stream with a lower bound limit
     * @author MohamedBassem
     */
    public function testFilterStream_SelectWithDate()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=3&lastDate=2014-01-1%2012:00:00',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content, "Wrong Json Format");
        $json = json_decode($content, true);
        $this->assertArrayHasKey('count', $json, "count not found");
        $this->assertArrayHasKey('requests', $json, "requests not found");

        $this->assertEquals(2, $json['count'], "Wrong number of results in count");

        $this->assertArrayHasKey('id', $json['requests'][0], "request id not found");
        $this->assertArrayHasKey('username', $json['requests'][0], "requester username not found");
        $this->assertArrayHasKey('userId', $json['requests'][0], "requester userId not found");
        $this->assertArrayHasKey('description', $json['requests'][0], "request description not found");
        $this->assertArrayHasKey('offersCount', $json['requests'][0], "request offercount not found");
        $this->assertArrayHasKey('price', $json['requests'][0], "request price not found");
        $this->assertArrayHasKey('date', $json['requests'][0], "request date not found");

        $this->assertEquals(2, count($json['requests']), "Wrong number of results in request array");
    }


    /*
     * Test Case testing filtering stream if there is no match for the query.
     * @author MohamedBassem
     */
    public function testFilterStream_FilterNoRequests()
    {
        $this->addFixture(new LoadFilterStreamData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            '/tangle/1/request?limit=3&query=z',
            array(),
            array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession1'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Success status code");
        $content = $client->getResponse()->getContent();
        $this->assertJson($content, "Wrong Json Format");
        $json = json_decode($content, true);
        $this->assertArrayHasKey('count', $json, "count not found");
        $this->assertArrayHasKey('requests', $json, "requests not found");

        $this->assertEquals(0, $json['count'], "Wrong number of results in count");
        $this->assertEquals(0, count($json['requests']), "Wrong number of results in request array");
    }

}
