<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadDeleteRequestData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

/*
 * Test Class for Request Controller
 * @author OmarElAzazy
 */
class RequestControllerTest extends EntangleTestCase
{

    /*
     * Test case testing sending a request to delete request end point
     * with no session id header
     * @author OmarElAzazy
     */
    public function testDeleteAction_NoSessionId(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/1');

        $this->assertEquals(400, $client->getResponse()->getStatusCode(),
            'Check for status code of bad request for no session id');
    }

    /*
     * Test case testing sending a request to delete request end point
     * with bad session id
     * @author OmarElAzazy
     */
    public function testDeleteAction_BadSessionId(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'badSessionId'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),
            'Check for status code of bad request for bad session id');
    }

    /*
     * Test case testing sending a request to delete request end point
     * with expired session id
     * @author OmarElAzazy
     */
    public function testDeleteAction_ExpiredSessionId(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sessionUser1Expired'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),
            'Check for status code of bad request for expired session id');
    }

    /*
     * Test case testing sending a request to delete request end point
     * with bad request id
     * @author OmarElAzazy
     */
    public function testDeleteAction_BadRequestId(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/55',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sessionUser1'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),
            'Check for status code of bad request for bad request id');
    }
}
