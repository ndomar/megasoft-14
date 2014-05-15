<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadMarkAsDoneData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

/**
 * Test class for Offer Controller
 * @author mohamedzayan
 */
class OfferControllerTest extends EntangleTestCase {

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
