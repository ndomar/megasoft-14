<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Megasoft\EntangleBundle\DataFixtures\ORM\LoadVerificationData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;


class VerificationControllerTest extends EntangleTestCase
{
    
    public function testEmailVerification_verified(){
        $this->addFixture(new LoadVerificationData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET','/verify/123456' );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $htmlWebPage = $client->getResponse()->getContent();
        $this->assertContains('Congratulations, sampleUser you have been verified,Welcome to Entangle!',$htmlWebPage,'Verified!');
    }
    public function testEmailVerification_expired(){
        $this->addFixture(new LoadVerificationData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET','/verify/1234567' );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $htmlWebPage = $client->getResponse()->getContent();
        $this->assertContains('Woops, Something went wrong either this link has expired or user has been already verified.',$htmlWebPage,'Expired!');
    }
    public function testEmailVerification_notFound(){
        $this->addFixture(new LoadVerificationData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET','/verify/12345' );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $htmlWebPage = $client->getResponse()->getContent();
        $this->assertContains('This user does not exist,or it disappeared into a black hole.',$htmlWebPage,'Not Found!');
    }
}
