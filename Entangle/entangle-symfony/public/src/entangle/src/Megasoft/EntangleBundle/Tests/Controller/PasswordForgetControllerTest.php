<?php
/**
 * Created by PhpStorm.
 * User: karee_000
 * Date: 5/15/14
 * Time: 4:07 PM
 */

namespace Megasoft\EntangleBundle\Tests\Controller;


use Megasoft\EntangleBundle\DataFixtures\ORM\LoadForgetPasswordCodeData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserEmailData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

class PasswordForgetControllerTest extends EntangleTestCase {

    public function testForgetPasswordAction_WrongEmail(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->loadFixtures();

        $email= array("email"=>"wrong@Wrong.com");
        $emailJson=json_encode($email);
        $client = static::createClient();

        $client->request('POST','/user/forgetPass',array(), array(), array(),$emailJson );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testForgetPasswordAction_NewPasswordForgetRecord(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->addFixture(new LoadForgetPasswordCodeData());
        $this->loadFixtures();

        $email= array("email"=>"sample2@sample.com");
        $emailJson=json_encode($email);

        $client = static::createClient();

        $client->request('POST','/user/forgetPass',array(), array(), array(),$emailJson );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
    public function testForgetPasswordAction_UpdatePasswordForgetRecord(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->addFixture(new LoadForgetPasswordCodeData());
        $this->loadFixtures();

        $email= array("email"=>"sample@sample.com");
        $emailJson=json_encode($email);
        $client = static::createClient();

        $client->request('POST','/user/forgetPass',array(), array(), array(),$emailJson );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordAction_404(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->addFixture(new LoadForgetPasswordCodeData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET','/reset/5wshJEh6dPU2MT8PFfgdddffJlp2VMngyXImF',array(), array(), array());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordAction_Succsess(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->addFixture(new LoadForgetPasswordCodeData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET','/reset/5wshJEh6dPU2MT8PFJlp2VMngyXImF',array(), array(), array());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordAction_expired(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->addFixture(new LoadForgetPasswordCodeData());
        $this->loadFixtures();

        $client = static::createClient();

        $client->request('GET','/reset/thisISaSAMPLEpasswordCODE',array(), array(), array());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}