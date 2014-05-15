<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadFilterStreamData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadLeaveTangleData;
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
    
    /**
     * Test Case testing not sending a tangle id in the request
     * @author HebaAamer 
     */
    public function testLeaveTangleAction_WrongTangleId() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/3/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode(), 'Wrong tangle id');
    }
    
    /**
     * Test Case testing not sending a session id in the 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_NullSessionId() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Not sending session id');
    }
    
    /**
     * Test Case testing sending expired session 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_ExpiredSession() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Sending expired session');
    }
    
    /**
     * Test Case testing sending wrong session 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_WrongSession() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'mohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'Sending wrong session');
    }
    
    /**
     * Test Case testing user not in the tangle 
     * @author HebaAamer
     */
    public function testLeaveTangleAction_NotUserInTangle() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userMazen'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'Case user not in the tangle');
    }
    
    /**
     * Test Case to check the condition of being a tangle owner
     * @author HebaAamer
     */
    public function testLeaveTangleAction_IsTangleOwner() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAhmad'));
        $response = $client->getResponse();
        $this->assertEquals(403, $response->getStatusCode(), 'Case user is the tangle owner');
    }
    
    /**
     * Test Case to check if the user left the tangle or not
     * @author HebaAamer
     */
    public function testLeaveTangleAction_LeftUser() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAdel'));
        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'Case left user');
    }
    
    /**
     * Test Case to check the success scenario
     * @author HebaAamer
     */
    public function testLeaveTangleAction_LeaveTangle() {
        $this->addFixture(new LoadLeaveTangleData());
        $this->loadFixtures();
        
        $client = static::createClient();
        $client->request('DELETE',
                'tangle/1/user',
                array(),
                array(),
                array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode(), 'Case sending a valid request');
        
        $doctrine = $this->doctrine;
        
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $tangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $claimRepo = $doctrine->Repository('MegasoftEntangleBundle:Claim');
        $messageRepo = $doctrine->Repository('MegasoftEntangleBundle:Message');
        
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => 1, 'userId' => 3));
        $tangle = $tangleRepo->findOneBy(array('id' => 1));
        
        $this->assertNotNull($userTangle->getLeavingDate(), 'Error in setting the leaving date');
        $this->assertEquals(-80, $tangle->getDeletedBalance(), 'Error in updating the tangle balance');
        
        $deletedRequests = $requestRepo->findBy(array('tangleId' => 1,
            'userId' => 3, 'deleted' => true, ));
        $requests = $requestRepo->findBy(array('tangleId' => 1,
            'userId' => 3, ));
        $this->assertEquals(count($requests), count($deletedRequests), 'Error in deleting all the requests');
        
        $claims = $claimRepo->findBy(array('tangleId' => 1, 'claimer' => 3, ));
        $deletedClaims = $claimRepo->findBy(array('tangleId' => 1, 'claimer' => 3, 'deleted' => true));
        $this->assertEquals(count($claims), count($deletedClaims), 'Error in deleting all the claims');
        
        foreach($requests as $request) {
            $requestOffers = $request->getOffers();
            foreach($requestOffers as $requestOffer) {
                $this->assertTrue($requestOffer->getDeleted(), 'Error in deleting an offer');
                $offerMessages = $requestOffer->getMessages();
                foreach ($offerMessages as $offerMessage) {
                    $this->assertTrue($offerMessage->getDeleted(), 'Error in deleting a message');
                }
            }
        }
        
        $offers = $offerRepo->findBy(array('userId' => 3));
        foreach ($offers as $offer) {
                $offerRequest = $requestRepo->findOneBy(array(
                    'id' => $offer->getRequestId(), 'tangleId' => 1, ));
                if ($offerRequest != null) {
                    $this->assertTrue($offer->getDeleted(), 'Error in deleting a user offer');
                    $offerMessages = $offer->getMessages();
                    foreach ($offerMessages as $offerMessage) {
                        $this->assertTrue($offerMessage->getDeleted(), 'Error in deleting a message');
                    }
                }
            }
        }
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

