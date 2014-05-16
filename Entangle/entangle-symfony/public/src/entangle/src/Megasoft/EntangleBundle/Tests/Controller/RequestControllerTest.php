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
            'Check for status code of unauthorized for bad session id');
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
            'Check for status code of unauthorized for expired session id');
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
            'Check for status code of unauthorized for bad request id');
    }

    /*
     * Test case testing sending a request to delete request end point
     * with user who is not the requester
     * @author OmarElAzazy
     */
    public function testDeleteAction_UserNotRequester(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/4',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sessionUser1'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),
            'Check for status code of unauthorized for request of another user');
    }

    /*
     * Test case testing sending a request to delete request end point
     * with id of already deleted request
     * @author OmarElAzazy
     */
    public function testDeleteAction_DeletedRequest(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/3',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sessionUser1'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(),
            'Check for status code of bad request for already deleted request');
    }

    /*
     * Test case testing sending a request to delete request end point
     * with id of a not opened request
     * @author OmarElAzazy
     */
    public function testDeleteAction_NotOpenedRequest(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/2',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sessionUser1'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode(),
            'Check for status code of bad request for deleting not opened request');
    }

    /*
     * Test case testing sending a request to delete request end point
     * and deletes request and its offers
     * @author OmarElAzazy
     */
    public function testDeleteAction_DeleteRequestAndOffers(){
        $this->addFixture(new LoadDeleteRequestData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('DELETE',
            '/request/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sessionUser1'));

        $this->assertEquals(204, $client->getResponse()->getStatusCode(),
            'Check for status code of deleted for deleting a request and its offers');

        $requestRepo = $this->doctrine->getRepository('MegasoftEntangleBundle:Request');

        $request = $requestRepo->findOneBy(array('id' => 1));
        $this->assertTrue($request->getDeleted(),
            'Check the request is deleted');

        $offerRepo = $this->doctrine->getRepository('MegasoftEntangleBundle:Offer');

        $offer = $offerRepo->findOneBy(array('id' => 1));
        $this->assertTrue($offer->getDeleted(),
            'Check the offers are deleted');
    }
}
