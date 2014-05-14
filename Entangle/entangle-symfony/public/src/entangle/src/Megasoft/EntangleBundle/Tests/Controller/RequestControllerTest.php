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

        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Check for status code of bad request');
    }
}
