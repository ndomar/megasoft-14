<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadFilterStreamData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadClaimData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

class ClaimControllerTest extends EntangleTestCase {

    /**
     * Tests wrong sessionId for GetClaims
     *
     * @author sak93
     */
    public function testGetClaimsAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET', '/tangle/1/claim', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession4',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking Wrong SessionId");
    }

    /**
     * Tests sending an expired session id 
     * @author sak93
     */
    public function testGetClaimsAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET', '/tangle/1/claim', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession1',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking Expired SessionId");
    }

    /**
     * Tests no sessionId for GetClaim
     *
     * @author sak93
     */
    public function testGetClaimsAction_NoSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET', '/tangle/1/claim', array(), array(), array('HTTP_X_SESSION_ID' => '',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking empty sessionId");
    }

    /**
     * Tests wrong user for GetClaim
     *
     * @author sak93
     */
    public function testGetClaimsAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET', '/tangle/1/claim', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession3',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'checking unauthorized user');
    }

    /**
     * Tests that there are no claims
     *
     * @author sak93
     */
    public function testGetClaimsAction_NoClaims() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET', '/tangle/1/claim', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession',));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $json_string = $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
    }

    /**
     * Tests correct scenario for get claims
     *
     * @author sak93
     */
    public function testGetClaimsAction_GetClaims() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET', '/tangle/1/claim', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession',));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string = $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string, true);
        $this->assertArrayHasKey('claims', $json, true, 'The claim is not found');
        $claim = $json['claims'][0];
        $this->assertArrayHasKey('claimerId', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('claimId', $claim, true, 'The offer information is not fount');
        $this->assertArrayHasKey('offerId', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('offererId', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('offererName', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('requesterId', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('requesterName', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('offerPrice', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('message', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('status', $claim, true, 'The tangle id is not found');
        $this->assertArrayHasKey('claimerName', $claim, true, 'The tangle id is not found');
    }

    /**
     * Tests wrong sessionId for resolveClaim
     *
     * @author sak93
     */
    public function testResolveClaimAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/claim/1/resolve', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession4',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking Wrong SessionId");
    }

    /**
     * Tests sending an expired session id for resolve Claim 
     * @author sak93
     */
    public function testResolveClaimAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/claim/1/resolve', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession1',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking Expired SessionId");
    }

    /**
     * Tests sending no session id 
     * @author sak93
     */
    public function testResolveClaimAction_NoSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/claim/1/resolve', array(), array(), array('HTTP_X_SESSION_ID' => '',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Checking empty sessionId");
    }

    /**
     * Tests wrong user for resolve claim
     *
     * @author sak93
     */
    public function testResolveClaimAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/claim/1/resolve', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession3',));
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'checking unauthorized user');
    }

    /**
     * Tests user not tangle owner
     *
     * @author sak93
     */
    public function testResolveClaimsAction_notTangleOwner() {
        $this->addFixture(new LoadClaimData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/claim/1/resolve', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession1',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

}
