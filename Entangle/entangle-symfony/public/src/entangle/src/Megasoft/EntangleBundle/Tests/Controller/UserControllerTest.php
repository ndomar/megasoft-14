<?php
/**
 * Created by PhpStorm.
 * User: almgohar
 * Date: 5/14/14
 * Time: 5:54 PM
 */

namespace Megasoft\EntangleBundle\Tests\Controller;


use Megasoft\EntangleBundle\DataFixtures\ORM\AddUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadOfferData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadRequestData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadSessionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTransactionData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

/**
 * Test class for UserController
 * @author Almgohar
 */
class UserControllerTest extends EntangleTestCase {

    /**
     * Tests sending a wrong session id to the GeneralProfileAction
     * @author Almgohar
     */
    public function testGeneralProfileAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession2',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Wrong SessionId");
    }

    /**
     * Tests sending an expired session id to the GeneralProfileAction
     * @author Almgohar
     */
    public function testGeneralProfileAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    /**
     * Tests sending no session id to the GeneralProfileAction
     * @author Almgohar
     */
    public function testGeneralProfileAction_NoSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking empty sessionId");
    }

    /**
     * Tests sending a different user id from the logged in one to the GeneralProfileAction
     * @author Almgohar
     */
    public function testGeneralProfileAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/2/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), 'checking unauthorized user');

    }

    /**
     * Tests sending a wrong user id to the GeneralProfileAction
     * @author Almgohar
     */
    public function testGeneralProfileAction_UserNotFound() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/5/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking required user not found");
    }


    /**
     * Tests sending a correct request to the GeneralProfileAction
     * @author Almgohar
     */
    public function testGeneralProfileAction_GetGeneralProfile() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            '/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('name',$json,true, 'The user name is not found');
        $this->assertArrayHasKey('description',$json,true, 'The user description is not fount');
        $this->assertArrayHasKey('photo',$json,true, 'The user photo is not fount');
        $this->assertArrayHasKey('verified',$json,true, 'The user verification is not fount');
        $this->assertEquals('sampleUser',$json['name']);
    }

    /**
     * Tests sending an expired session id to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    /**
     * Tests sending a wrong session id to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession4',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking wrong sessionId");
    }

    /**
     * Tests sending an empty session id to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_EmptySession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();

        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking empty sessionId");
    }

    /**
     * Tests sending a session id of a user not in the tangle of the required user to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession3',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking unauthorized user");
    }

    /**
     * Tests sending a not found tangle id to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_NotFoundTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/3/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking not found tangle");
    }

    /**
     * Tests sending a wrong tangle id (required user not in it) to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_WrongTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/2/user/1/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking wrong tangle");
    }

    /**
     * Tests sending a wrong tangle id (user not in it) to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_UserNotInTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/2/user/3/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking logged in user not in tangle");
    }

    /**
     * Tests sending a session id of a user not in the tangle of the required user to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_UserNotFound() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/5/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking required user not found");
    }

    /**
     * Tests sending a correct request to the ProfileAction
     * @author Almgohar
     */
    public function testProfileAction_GetProfile() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/2/profile',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('name',$json,true, 'The user name is not found');
        $this->assertArrayHasKey('description',$json,true, 'The user description is not fount');
        $this->assertArrayHasKey('photo',$json,true, 'The user photo is not fount');
        $this->assertArrayHasKey('verified',$json,true, 'The user verification is not fount');
        $this->assertEquals('sampleUser1',$json['name']);
    }

    /**
     * Tests sending an expired session id to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_ExpiredSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession1',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking Expired SessionId");
    }

    /**
     * Tests sending a wrong session id to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_WrongSession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession4'));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking wrong sessionId");
    }

    /**
     * Tests sending an empty session id to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_EmptySession() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'',));

        $this->assertEquals(401, $client->getResponse()->getStatusCode(),"Checking empty sessionId");
    }

    /**
     * Tests sending a session id of a user not in the tangle of the required user to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_WrongUser() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/1/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession3',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking unauthorized user");
    }

    /**
     * Tests sending a not found tangle id to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_NotFoundTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/3/user/1/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking not found tangle");
    }

    /**
     * Tests sending a wrong tangle id (required user not in it) to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_WrongTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/2/user/1/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking wrong tangle");
    }

    /**
     * Tests sending a wrong tangle id (user not in it) to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_UserNotInTangle() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/2/user/3/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(401, $client->getResponse()->getStatusCode(), "checking logged in user not in tangle");
    }

    /**
     * Tests sending a not found user id to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_UserNotFound() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/5/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "checking required user not found");
    }

    /**
     * Tests sending a correct request to the TransactionsAction
     * @author Almgohar
     */
    public function testTransactionsAction_getTransactions() {
        $this->addFixture(new LoadSessionData());
        $this->addFixture(new LoadUserData());
        $this->addFixture(new LoadTangleData());
        $this->addFixture(new LoadUserTangleData());
        $this->addFixture(new LoadRequestData());
        $this->addFixture(new LoadOfferData());
        $this->addFixture(new LoadTransactionData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('GET',
            'tangle/1/user/2/transactions',
            array(),
            array(),
            array('HTTP_X_SESSION_ID'=>'sampleSession',));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('transactions',$json,true, 'Transactions not found');
        $this->assertArrayHasKey('credit',$json,true, 'The user credit not fount');
        $this->assertNotEquals(0,count($json['transactions']),'No transactions found');
        $this->assertArrayHasKey('offerId',$json['transactions'][0], 'The offer id not found');
        $this->assertArrayHasKey('requesterName',$json['transactions'][0], 'The requester name not found');
        $this->assertArrayHasKey('offererName',$json['transactions'][0], 'The offerer name not found');
        $this->assertArrayHasKey('amount',$json['transactions'][0], 'The transaction amount not found');
        $this->assertArrayHasKey('photo',$json['transactions'][0], 'The requester photo not found');
        $this->assertArrayHasKey('requestId',$json['transactions'][0], 'The request id not found');
        $this->assertArrayHasKey('requesterId',$json['transactions'][0], 'The requester id not found');
        $this->assertEquals(1,count($json['transactions']),'The deleted transaction is shown');
    }

    public function testRegisterAction_successScenario() {
        $this->addFixture(new AddUserData());
        $this->loadFixtures();
        $client = static::createClient();
        $client->request('post','/register', array(), array(),array());
        $this->assertEquals(201, $client->getRequest()->getStatusCode());
        $json_string =  $client->getResponse()->getContent();
        $this->assertJson($json_string, 'Wrong json format');
        $json = json_decode($json_string,true);
        $this->assertArrayHasKey('username',$json,true, 'username not found');
        $this->assertArrayHasKey('email',$json,true, 'Email not found');
        $this->assertArrayHasKey('password',$json,true, 'Password not found');
        $this->assertArrayHasKey('confirmPassword',$json,true, 'Confirm Password not found');
        $em = $this->em;
        $userRepo = $em->getRepository('MegasoftEntangleBundle:User');
        $emailRepo = $em ->getRepository('MegasoftEntangleBundle:UserEmail');


    }

    public function testRegisterAction_emptyUsername() {
        $this->addFixture(new AddUserData());
        $this->loadFixtures();
        $usernameJson = array('username' => '');
        $usernameJsonBody = json_encode($usernameJson);
        $client = static::createClient();

        $client->request('post','/register', array(), array(),$usernameJsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Username field is empty');

    }

    public function testRegisterAction_emptyEmail() {
        $this->addFixture(new AddUserData());
        $this->loadFixtures();
        $usernameJson = array('email' => '');
        $usernameJsonBody = json_encode($usernameJson);
        $client = static::createClient();

        $client->request('post','/register', array(), array(),$usernameJsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Email field is empty');

    }

    public function testRegisterAction_emptyPassword() {
        $this->addFixture(new AddUserData());
        $this->loadFixtures();
        $usernameJson = array('password' => '');
        $usernameJsonBody = json_encode($usernameJson);
        $client = static::createClient();

        $client->request('post','/register', array(), array(),$usernameJsonBody);
        $this->assertEquals(400, $client->getResponse()->getStatusCode(), 'Password field is empty');

    }
    
}

