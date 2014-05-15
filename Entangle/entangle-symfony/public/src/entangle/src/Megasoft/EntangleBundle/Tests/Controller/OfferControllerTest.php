<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadMessageData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadOfferData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadRequestData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadMarkAsDoneData;

/**
 * Test class for Offer Controller
 * @author mohamedzayan
 */
class OfferControllerTest extends EntangleTestCase {

    /**
     * Tests sending a wrong session id to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession4',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Wrong SessionId");
    }

    /**
     * Tests sending an expired session id to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    /**
     * Tests sending no session id to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_NoSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking empty sessionId");
    }

    /**
     * Tests sending an unauthorized user id to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession3',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'checking unauthorized user');

    }

    /**
     * Tests sending a not found offer id to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_NotFoundOffer() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/4',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'checking not found offer');

    }

    /**
     * Tests sending a deleted offer id to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_DeletedOffer() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/3',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'checking deleted offer');

    }

    /**
     * Tests sending an offer id that is linked to a deleted request to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_DeletedRequest() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/3',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'checking deleted request');

    }

    /**
     * Tests sending a correct request to the offerAction
     * @author Almgohar
     */
    public function testOfferAction_GetOffer() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->addFixture(new LoadMessageData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/offer/1',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('tangleId',$json,true, 'The tangle id is not found');
        $this->assertArrayHasKey('offerInformation',$json,true, 'The offer information is not fount');
        $this->assertArrayHasKey('comments',$json,true, 'The offer comments are not fount');
        $this->assertEquals(1, $json['tangleId'], 'the tangle id is wrong');
        $this->assertNotEquals(0, $json['comments'],'The comments are not sent');
        $this->assertArrayHasKey('offererAvatar', $json['offerInformation'], 'The offerer avatar is not found');
        $this->assertArrayHasKey('offererName', $json['offerInformation'], 'The offerer name is not found');
        $this->assertArrayHasKey('offerDescription', $json['offerInformation'], 'The offer description is not found');
        $this->assertArrayHasKey('offerDeadline', $json['offerInformation'], 'The offer deadline is not found');
        $this->assertArrayHasKey('offerStatus', $json['offerInformation'], 'The offer status is not found');
        $this->assertArrayHasKey('requesterId', $json['offerInformation'], 'The requester id is not found');
        $this->assertArrayHasKey('offerPrice', $json['offerInformation'], 'The offer price is not found');
        $this->assertArrayHasKey('offererId', $json['offerInformation'], 'The offerer id is not found');
        $this->assertArrayHasKey('offerDate', $json['offerInformation'], 'The offer date is not found');
        $this->assertArrayHasKey('requestStatus', $json['offerInformation'], 'The request status is not found');
        $this->assertEquals(1, count($json['comments']),'The deleted comment is shown.');
    }

    /**
     * Test Case testing sending a null session to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_NullSession() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/1', array(), array(), array());

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a wrong session to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_WrongSession() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/1', array(), array(), array('HTTP_X_SESSION_ID' => 'wrongSession'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending an offer with a deleted request to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_DeletedRequest() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/2', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending an offer with a deleted request to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_ClosedRequest() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/3', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a non exsisting offer to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_InvalidOffer() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/9999', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a deleted offer to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_DeletedOffer() {
        $this->addFixture(new LoadMarkASDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/8', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending an already marked as done offer to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_OfferAlreadyDone() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/4', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a pending offer to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_OfferPending() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/5', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a rejected offer to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_OfferRejected() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/7', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a failed offer to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_OfferFailed() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/6', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing sending a session of a user other than the requester to updateAction
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_InvalidRequester() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/6', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession2'));
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test Case testing the success scenario
     * @author mohamedzayan
     */
    public function testMarkOfferAsDone_OfferMarkedSuccessfully() {
        $this->addFixture(new LoadMarkAsDoneData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('POST', '/markAsDone/offer/1', array(), array(), array('HTTP_X_SESSION_ID' => 'sampleSession'));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $em = $this->em;
        $repo = $em->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $repo->find(1);
        $status = $offer->getStatus();
        $this->assertEquals(1, $status);
        $usertangletable = $em->getRepository('MegasoftEntangleBundle:UserTangle');
        $request = $offer->getRequest();
        $tangleId = $request->getTangleId();
        $offerer = $usertangletable->findOneBy(array('userId' => $offer->getUserId(), 'tangleId' => $tangleId));
        $offerercredit = $offerer->getCredit();
        $this->assertEquals($offerercredit, $offer->getRequestedPrice());
    }

}