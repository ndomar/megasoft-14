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
/*
 * Test Class for PasswordForget Controller
 * @author KareemWahby
 */
class PasswordForgetControllerTest extends EntangleTestCase {

    public function testForgetPasswordAction_WrongEmail(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->loadFixtures();

        $email= array("email"=>"wrong@Wrong.com");
        $emailJson=json_encode($email);
        $client = static::createClient();

        $client->request('POST','/user/forgetPass',array(), array(), array(),$emailJson );
        $emailRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $emailFromRepo=$emailRepo->findOneBy(array("email"=>$email));
        $this->assertEquals(null,$emailFromRepo);
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

        $forgetRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:ForgetPasswordCode');
        $emailRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $userID=$emailRepo->findOneBy(array("email"=>$email))->getUserId();
        $forgetPassCode=$forgetRepo->findOneBy(array("userId"=>$userID));
        $this->assertNotEquals(null,$forgetPassCode);
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

        $forgetRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:ForgetPasswordCode');
        $forgetPassCode=$forgetRepo->findOneBy(array("forgetPasswordCode"=>"5wshJEh6dPU2MT8dhdhPFfgdddffJlp2VMngyXImF"));

        $this->assertEquals(null,$forgetPassCode);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testResetPasswordAction_Succsess(){
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadUserEmailData());
        $this->addFixture(new LoadForgetPasswordCodeData());
        $this->loadFixtures();

        $forgetRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:ForgetPasswordCode');
        $forgetPassCode=$forgetRepo->findOneBy(array("forgetPasswordCode"=>"5wshJEh6dPU2MT8PFJlp2VMngyXImF"));
        $expired=$forgetPassCode->getExpired();
        $this->assertEquals(0,$expired);
        $this->assertNotEquals(null,$forgetPassCode);
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

        $forgetRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:ForgetPasswordCode');
        $forgetPassCode=$forgetRepo->findOneBy(array("forgetPasswordCode"=>"5wshJEh6dPU2MT8PFJlp2VMngyXImF"));
        $expired=$forgetPassCode->getExpired();
        $this->assertEquals(0,$expired);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    //TODO figure out how the form encodes the data
//    public function testChangePasswordAction_matchingPasswords(){
//        $this->addFixture(new LoadUserData());
//        $this->addFixture(new LoadUserEmailData());
//        $this->addFixture(new LoadForgetPasswordCodeData());
//        $this->loadFixtures();
//        $client = static::createClient();
//        $formData=array("newPass"=>"test","newPassR"=>"test1","Username"=>"sampleUser");
//       $client->request('POST','/reset',array(), array(), array());
//        $client->request("POST","/reset",$formData);
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $userRepo=$this->doctrine->getRepository('MegasoftEntangleBundle:User');
//        $user=$userRepo->findOneBy(array("name"=>"sampleUser"));
//        $this->assertEquals("test",$user->getPassword());
//    }
//    public function testChangePasswordAction_misMatchingPasswords(){
//        $this->addFixture(new LoadUserData());
//        $this->addFixture(new LoadUserEmailData());
//        $this->addFixture(new LoadForgetPasswordCodeData());
//        $this->loadFixtures();
//        $client = static::createClient();
//        $formData=array("newPass"=>"test","newPassR"=>"test1","Username"=>"sampleUser");
//        $client->request('POST','/reset',array(), array(), array(),$formData);
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }
//    public function testChangePasswordAction_missingFields(){
//        $this->addFixture(new LoadUserData());
//        $this->addFixture(new LoadUserEmailData());
//        $this->addFixture(new LoadForgetPasswordCodeData());
//        $this->loadFixtures();
//        $client = static::createClient();
//        $formData=array("newPass"=>"test","newPassR"=>"test1","Username"=>"sampleUser");
//        $client->request('POST','/reset',array(), array(), array(),$formData);
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }
}