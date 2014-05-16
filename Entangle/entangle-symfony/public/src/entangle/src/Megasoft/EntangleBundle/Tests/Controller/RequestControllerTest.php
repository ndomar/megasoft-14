<?php
/**
 * Created by PhpStorm.
 * User: mansour
 * Date: 5/16/14
 * Time: 2:51 AM
 */

namespace Megasoft\EntangleBundle\Tests\Controller;


use Megasoft\EntangleBundle\DataFixtures\ORM\LoadReopenRequestActionData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

class RequestControllerTest extends EntangleTestCase{

    /**
     * Checks wrong session entry.
     * @author Mansour
     */
    public function testReopenRequestAction_WrongSession()
    {
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/2/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'wrongSession'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Wrong User Session");
    }

    /**
     * Checks empty session entry.
     * @author Mansour
     */
    public function testReopenRequestAction_EmptySession()
    {
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/2/reopen', array(), array(), array());
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Session Header");
    }

    /**
     * Checks null session entry.
     * @author Mansour
     */
    public function testReopenRequestAction_NullSession()
    {
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/2/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => NULL));
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Session Header");
    }

    /**
     * Check expired session entry.
     * @author Mansour
     */
    public function testReopenRequestAction_ExpiredSession()
    {
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/2/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession3'));
        $this->assertEquals(440, $client->getResponse()->getStatusCode(), "Expired Session");
    }

    public function testReopenRequestAction_InvalidUser()
    {
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/2/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession4'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Unauthorized User");
    }

    public function testRestReopenRequestAction_InvalidRequest(){
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/100/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession2'));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Invalid Request");
    }

    public function testRestReopenRequestAction_OpenedRequest(){
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/1/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession2'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Request is already opened");
    }

    public function testRestReopenRequestAction_FrozenRequest(){
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/3/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession2'));
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Request is already frozen");
    }

    public function testRestReopenRequestAction_reopenRequest(){
        $this->addFixture(new LoadReopenRequestActionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/request/2/reopen', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession2'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error reopening request");
    }

} 