<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadChangeOfferPriceData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

class OfferControllerTest extends EntangleTestCase
{
    public function testChangeOfferPriceAction_WrongSession()
    {
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'wrongSession'), $jsonBody);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Wrong Session");
    }

    public function testChangeOfferPriceAction_EmptySession()
    {
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(), array(), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Session");
    }

    public function testChangeOfferPriceAction_NullSession()
    {
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => NULL), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Session");
    }

    public function testChangeOfferPriceAction_ExpiredSession()
    {
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession2'), $jsonBody);
        $this->assertEquals(440, $client->getResponse()->getStatusCode(), "Expired Session");
    }

    public function testChangeOfferPriceAction_InvalidOffer()
    {
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1000/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Offer Not Found");
    }

    public function testChangeOfferPriceAction_SamePrice(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '500');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(409, $client->getResponse()->getStatusCode(), "Same Price");
    }

    public function testChangeOfferPriceAction_InvalidUser(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession3'), $jsonBody);
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "Invalid User");
    }

    public function testChangeOfferPriceAction_AcceptedOffer(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/3/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Accepted Offer");
    }

    public function testChangeOfferPriceAction_DoneOffer(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/2/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Offer Was Done");
    }

    public function testChangeOfferPriceAction_FailedOffer(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/4/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Failed Offer");
    }

    public function testChangeOfferPriceAction_RejectedOffer(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '10');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/5/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Rejected Offer");
    }

    public function testChangeOfferPriceAction_EmptyPrice(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array();
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Empty Price");
    }

    public function testChangeOfferPriceAction_NullPrice(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => NULL);
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Null Price");
    }

    public function testChangeOfferPriceAction_NonNumericPrice(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => 'abcd');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), "Non-Numeric Price");
    }

    public function testChangeOfferPriceAction_changePrice(){
        $this->addFixture(new LoadChangeOfferPriceData());
        $this->loadFixtures();
        $body = array('newPrice' => '300');
        $jsonBody = json_encode($body);
        $client = static::createClient();
        $client->request('POST', '/offers/1/changePrice', array(), array(),
            array('HTTP_X_SESSION_ID' => 'sampleSession'), $jsonBody);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Changing Price Error");
    }

}
