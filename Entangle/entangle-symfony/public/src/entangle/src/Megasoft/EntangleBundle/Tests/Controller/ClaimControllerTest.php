<?php

use Megasoft\EntangleBundle\Tests\EntangleTestCase;

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\Tests\EntangleTestCase;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadClaimData;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class tests the claim controller.
 * @author Salma Amr
 */
class ClaimControllerTest extends EntangleTestCase {

    /**
     * Tests whether the requestID in null
     * @author Salma Amr
     */
    public function testCreateClaimAction_NullRequestId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/ssss/sendClaim/1/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'null requestId');
    }

    /**
     * Tests whether the requestId is right or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_WrongRequestId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/100/sendClaim/1/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'wrong requestId');
    }

    /**
     * Tests whether the offer id is null or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_NullOfferId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/1/sendClaim/ssss/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'null offerId');
    }

    /**
     * Tests whether the offer id is wrong or not
     * @author Salma Amr 
     */
    public function testCreateClaimAction_WrongOfferId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/1/sendClaim/100/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'wrong offerId');
    }

    /**
     * Tests whether the session id is null or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_NullSessionId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/3/sendClaim/3/user', array(), array(), array());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'null sessionId');
    }

    /**
     * Tests whether the session id is expired or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_ExpiredSession() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/3/sendClaim/3/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'expired session');
    }

    /**
     * Tests whether the session id is right or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_WrongSession() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/3/sendClaim/3/user', array(), array(), array('HTTP_X_SESSION_ID' => 'wrong'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'wrong session');
    }

    /**
     * Tests whether the status of the offer is accepted or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_WrongOfferStatus() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/2/sendClaim/2/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'offer is not accepted');
    }

    /**
     * Tests whether the offer is deleted or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_OfferDeleted() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/6/sendClaim/6/user', array(), array(), array('HTTP_X_SESSION_ID', 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'offer is deleted');
    }

    /**
     * Tests whether the claimer is in a tangle or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_UserNotInTangle() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('POST', 'claim/6/sendClaim/7/user', array(), array(), array('HTTP_X_SESSION_ID', 'userAhmad'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'user not in tangle');
    }

    /**
     * Tests whether the claim is created or not
     * @author Salma Amr
     */
    public function testCreateClaimAction_CreateClaim() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $body = array('claimMessage' => 'ana bakrahko kolloko');
        $jsonBody = json_encode($body);

        $client = static::createClient();
        $client->request('POST', 'claim/3/sendClaim/3/user', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'), $jsonBody);

        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode(), 'claim not created');
    }

    /**
     * Tests whether the offer id is null
     * @author Salma Amr
     */
    public function testClaimRenderAction_NullOfferId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/1/claim/ssss/offer', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'null offerId');
    }

    /**
     * Tests whether the claim id is null
     * @author Salma Amr
     */
    public function testClaimRenderAction_NullClaimId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/ssss/claim/3/offer', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'null claimId');
    }

    /**
     * Tests whether the session is null or not
     * @author Salma Amr
     */
    public function testClaimRenderAction_NullSession() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/1/claim/3/offer', array(), array(), array());
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'null sessionId');
    }

    /**
     * Tests whether the session is expired or not
     * @author Salma Amr
     */
    public function testClaimRenderAction_ExpiredSession() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/1/claim/3/offer', array(), array(), array('HTTP_X_SESSION_ID', 'userMohamed'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'expired session');
    }

    /**
     * Tests for a wrong session id
     * @author Salma Amr
     */
    public function testClaimRenderAction_WrongSession() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/1/claim/3/offer', array(), array(), array('HTTP_X_SESSION_ID', 'wrong session'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'wrong session');
    }

    /**
     * Tests for a wrong claim id
     * @author Salma Amr
     */
    public function testClaimRenderAction_WrongClaimId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/1/claim/4/offer', array(), array(), array('HTTP_X_SESSION_ID', 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'wrong claim id');
    }

    /**
     * Tests for a wrong offer id
     * @author Salma Amr
     */
    public function testClaimRenderAction_WrongOfferId() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/0/claim/3/offer', array(), array(), array('HTTP_X_SESSION_ID', 'userAly'));
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'wrong offer id');
    }

    /**
     * Tests that the data were fetched correctly
     * @author Salma Amr
     */
    public function testClaimRenderAction_FetchData() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/1/claim/3/offer', array(), array(), array('HTTP_X_SESSION_ID' => 'userAly'));

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'something went wrong');
    }

    /**
     * Tests for an invalid email
     * @author Salma Amr
     */
    public function testClaimRenderAction_InvalidEmail() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET', 'claimReport/5/claim/7/offer', array(), array(), array('HTTP_X_SESSION_ID' => 'userFahmy'));

        $response = $client->getResponse();
        $this->assertEquals(401, $response->getStatusCode(), 'invalid email');
    }

}
