<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Transaction;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\User;


class UserController extends Controller {

    /**
     * Validates the username and password from request and returns sessionID
     * @param  Integer $len length for the generated sessionID
     * @return String $generatedSessionID the session id that will be used
     * 
     * @author maisaraFarahat
     */
    private function generateSessionId($len) {
        $generatedSessionID = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < $len; $i++) {
            $generatedSessionID .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $generatedSessionID;
    }

    /**
     * Validates the username and password from request and returns sessionID
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response $response
     * 
     * @author maisaraFarahat
     */
    public function loginAction(Request $request) {

        $response = new JsonResponse();
        if (!$request) {
            return new Response('Bad Request', 400);
        }
        $json = $request->getContent();
        if (!$json) {
            return new Response('Bad JSON Request', 400);
        }
        $json_array = json_decode($json, true);
        $username = $json_array['username'];
        $password = $json_array['password'];

        if (!$username) {
            return new Response('Missing Username', 400);
        }
        if (!$password) {
            return new Response('Missing Password', 400);
        }
        if (strstr("\"", $username) || strstr("'", $username)) {
            return new Response('
            usernames should not have any special characters', 400);
        }
        $sessionId = $this->generateSessionId(30);

        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->findOneBy(array('name' => $username, 'password' => $password));
        if($user == null){
            return new Response("Bad Credentials",400);
        }
        
        $session = new Session();
        $session->setSessionId($sessionId)
                ->setUser($user)
                ->setExpired(false)
                ->setDeviceType("test")
                ->setRegId("test")
                ->setCreated(date("Y-m-d H:i:s"));
                
        $user->addSession($session);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        $response->setData(array('sessionId' => $sessionId,"userId"=>$user->getId()));
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * checks for the sessionID and gets the user 
     * @param \Symfony\Component\HttpFoundation\Request $request request containing the sessionId
     * @return \Symfony\Component\HttpFoundation\Response $response response containing: 
     * user with sessionID , date of birth , emails , userID and description
     * 
     * @author maisaraFarahat
     */
    public function whoAmIAction(Request $request) {
        $json = $request->getContent();
        if (!json)
            return new Response(400, 'request was null');
        $json_array = json_decode($json, true);
        $sessionId = $json_array['session_id'];
        if (!$sessionId) {
            return new Response(400, 'sessionID was null');
        } else {
            $doctrine = $this->getDoctrine();
            $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
            $retrievedSession = $repo->findOneBy(array('sessionId' => $sessionId));
            $user = $retrievedSession->getUser();
            if ($user == null) {
                return new Response('Bad Request', 400);
            } else {

                $response = new JsonResponse();
                $emails = $user->getEmails();
                $description = $user->getUserBio();
                $username = $user->getName();
                $dob = $user->getBirthDate();
                $userId = $user->getId();

                $response->setData(array('user' => $user, 'user_id' => $userId,
                    'date_of_birth' => $dob, 'description' => $description,
                    'username' => $username, 'emails' => $emails));

                $response->setStatusCode(200);
                return $response;
            }
        }
    }

    /**
     * Validates that a given user is a member of a given tangle
     * @param integer $userId
     * @param integer $tangleId
     * @return boolean true if the user is a memeber of this tangle, false otherwise
     * @author Almgohar
     */
    public function validateUser($userId, $tangleId) {
         $userTangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:UserTangle');
          $userTangle = $userTangleTable->
                findOneBy(array ('userId'=>$userId,'tangleId'=>$tangleId)); 
          if ($userTangle == null) {
              return false;
          } else {
              return true;
          }
    }
    
    /**
     * Validates the existence of a given tangle
     * @param integer $tangleId
     * @return boolean true if the tangle exists, false otherwise
     * @author Almgohar
     */
    public function validateTangle($tangleId) {
        $tangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleTable->findOneBy(array ('tangleId'=>$tangleId));
        if ($tangle == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Sends the required information in a JSon response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $userId
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author Almgohar
     */
    public function getProfileAction (Request $request, $userId, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if ($sessionId == null) {
            return new Response('Unauthorized',401);
        }
        
        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId'=>$sessionId));
        $loggedInUser = $session->getUserId();
        
        if (!$this->validateTangle($tangleId)) {
            return new Response('Tangle not found',404);
        }
        
        if (!$this->validateUser($loggedInUser, $tangleId)) {
            return new Response('You are not a member of this tangle', 401);
        }
        
        if (!$this->validateUser($userId, $tangleId)) {
            return new Response('The requested user is not a member of this tangle', 401);
        }
        
        $user = $userTable->findOneBy(array('id'=> $userId));
        $offers = $user->getOffers();
        $info = $this->getUserInfoAction($user, $tangleId);
        $transactions = $this->getTransactionsAction($offers, $tangleId);
        $response = new JsonResponse();
        $response->setData(array('information'=> $info,
            'transactions'=>$transactions));
        $response->setStatusCode(200);
        return $response;       
    }
    
    /**
     * Gets the user's transactions in a given tangle
     * @param array $offers
     * @param integer $tangleId
     * @return array of arrays $transactions
     * @author Almgohar
     */
    public function getTransactions ($offers, $tangleId) {
        $transactions = array();
        for ($i = 0; i < count($offers); $i++) {
            $offer = $offers[i];
            if ($offer == null) {
                continue;
            }
            if (($offer->getRequest()->getTangleId() == $tangleId)
                    && ($offer->getTransaction() != null)) {
                $requesterName =$offer->getRequest()->getUser()->getName();
                $requestDescription = $offer->getRequest()->getDescription();
                $amount = $offer->getTransaction()->getFinalPrice();
                $transactions[] = array('offerId'=>$offer->getId(),
                    'requesterName'=> $requesterName,
                    'requestDescription'=>$requestDescription,
                    'amount'=>$amount);
            }           
        }
        return $transactions;    
    }
    
    /**
     * Gets the basic information of a given user in a give tangle
     * @param user $user
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response | array #info
     * @author Almgohar
     */
    public function getUserInfo($user, $tangleId) {
        if ($user == null) {
            return new Response('Bad Request',400);
        }
        $doctrine = $this->getDoctrine();
        $userId = $user->getId();
        $userTangleTable = $doctrine->
                getRepository ('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleTable->
                findOneBy(array ('userId'=>$userId,'tangleId'=>$tangleId)); 
        $name = $user->getName();
        $description = $user->getUserBio();
        $credit = $userTangle->getCredit();
        $photo = $user->getPhoto();
        $birthdate = $user->getBirthDate();
        $verfied = $user->getVerified();
        $info = array ('name'=>$name , 'description'=>$description , 
            'credit'=>$credit, 'photo'=>$photo , 'birthdate'=>$birthdate , 
            'verified' => $verfied);
        return $info;
    }  
}
