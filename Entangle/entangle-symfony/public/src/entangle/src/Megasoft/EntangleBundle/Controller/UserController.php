<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Transaction;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse $response
     * 
     * @author maisaraFarahat
     */
    public function loginAction(\Symfony\Component\HttpFoundation\Request $request) {
        $response = new JsonResponse();
        $badReq = "bad request";
        if (!$request) {
            return new JsonResponse($badReq, 400);
        }
        $json = $request->getContent();
        if (!$json) {
            $response->setStatusCode(400, $badReq);
            return $response;
        }
        $json_array = json_decode($json, true);
        $name = $json_array['name'];
        $password = $json_array['password'];

        if (!$name) {
            return new JsonResponse("missing name", 400);
        }
        if (!$password) {
            return new JsonResponse("missing password", 400);
        }
        if (strstr("\"", $name) || strstr("'", $name)) {
            return new JsonResponse("the name has special characters", 400);
        }
        $sessionId = $this->generateSessionId(30);

        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->findOneBy(array('name' => $name, 'password' => $password));
        if (!$user) {
            return new JsonResponse("Wrong credentials", 400);
        }
        $session = new Session();
        $session->setSessionId($sessionId);
        $session->setUser($user);
        $session->setUserId($user->getId());
        $session->setCreated(new \DateTime('now'));
        $session->setExpired(0);
        $session->setRegId("ToAvoidNull");
        $session->setDeviceType("Galaxy S3");

        $user->addSession($session);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($session);

        $this->getDoctrine()->getManager()->flush();

        $kernel = $this->get('kernel');
        $filepath = 'http://entangle.io/images/profilePictures/';

        $response->setData(array('sessionId' => $sessionId, 'userId' => $user->getId()
            , 'profileImage' => $filepath . $user->getPhoto(),
            'username' => $user->getName() , ));
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * Validates that a given user is a member of a given tangle
     * @param integer $userId
     * @param integer $tangleId
     * @return boolean true if the user is a memeber of this tangle, false otherwise
     * @author Almgohar
     */
    private function validateUser($userId, $tangleId) {
        $userTangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleTable->
                findOneBy(array('userId' => $userId, 'tangleId' => $tangleId));
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
    private function validateTangle($tangleId) {
        $tangleTable = $this->getDoctrine()->
            getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleTable->findOneBy(array('id' => $tangleId));
        if ($tangle == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Gets the general profile of the logged in user
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $userId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author Almgohar
     */
    public function generalProfileAction(\Symfony\Component\HttpFoundation\Request $request, $userId) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response('Unauthorized', 401);
        }
        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user = $userTable->findOneBy(array('id' => $userId));
        if ($user == null) {
            return new Response('User not found', 404);
        }
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        $loggedInUser = $session->getUser();
        if ($session == null || $session->getExpired() || $loggedInUser != $user) {
            return new Response('Unauthorized', 401);
        }
        return $this->viewProfile($user);
    }
    
    /**
     * Gets the profile of a user in a given tangle
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $userId
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author Almgohar
     */
    public function profileAction(\Symfony\Component\HttpFoundation\Request $request, $userId, $tangleId) {
      $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response('Unauthorized', 401);
        }
        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user = $userTable->findOneBy(array('id' => $userId,));
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId,));
        $loggedInUser = $session->getUser();
        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
        }
        if ($user == null) {
            return new Response('User not found', 404);
        }
        if (!$this->validateTangle($tangleId)) {
            return new Response('Tangle not found', 404);
        }
        if (!$this->validateUser($loggedInUser->getId(), $tangleId)) {
            return new Response('You are not a member of this tangle', 401);
        }
        if (!$this->validateUser($userId, $tangleId)) {
            return new Response('The requested user is not a member of this tangle', 401);
        }
        return $this->viewProfile($user);
        
    }

    /**
     * Gets the basic information of a given user in a give tangle
     * @param user $user
     * @return \Symfony\Component\HttpFoundation\Response | JsonResponse $response
     * @author Almgohar
     */
    private function viewProfile($user) {
        if ($user == null) {
            return new Response('Bad Request', 400);
        }
        $name = $user->getName();
        $description = $user->getUserBio();
        $photo = $user->getPhoto();
        $birthDate = $user->getBirthDate()->format('d/m/Y');
        $verified = $user->getVerified();
        $information = array('name' => $name, 'description' => $description,
            'photo' => $photo, 'birth_date' => $birthDate,
            'verified' => $verified,);
        $response = new JsonResponse();
        $response->setData($information);
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * Gets the user's transactions in a given tangle
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $userId
     * @param integer $tangleId
     * @return array of arrays $transactions
     * @author Almgohar
     */
    public function transactionsAction(\Symfony\Component\HttpFoundation\Request $request, $userId, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response('Unauthorized', 401);
        }
        $doctrine = $this->getDoctrine();
        $userTangleTable = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId,));
        $user = $userTable->findOneBy(array('id' => $userId,));
        $userTangle = $userTangleTable->findOneBy(array('userId' => $userId, 'tangleId' => $tangleId,));
        $loggedInUser = $session->getUser();
        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
        }
        if (!$this->validateTangle($tangleId)) {
            return new Response('Tangle not found', 404);
        }
        if (!$this->validateUser($loggedInUser->getId(), $tangleId)) {
            return new Response('You are not a member of this tangle', 401);
        }
        if (!$this->validateUser($userId, $tangleId)) {
            return new Response('The requested user is not a member of this tangle', 401);
        }
        $transactions = array();
        $offers = $user->getOffers();
        $credit = $userTangle->getCredit();
        for ($i = 0; $i < count($offers); $i++) {
            $offer = $offers[$i];
            if (($offer->getRequest()->getTangleId() == $tangleId) && ($offer->getTransaction() != null)) {
                $requesterName = $offer->getRequest()->getUser()->getName();
                $requestDescription = $offer->getRequest()->getDescription();
                $amount = $offer->getTransaction()->getFinalPrice();
                $requestId = $offer->getRequest() . getId();
                $requesterId = $offer->getRequest() . getUserId();
                $transactions[] = array('offerId' => $offer->getId(),
                    'requesterName' => $requesterName,
                    'requestDescription' => $requestDescription,
                    'amount' => $amount, 'requestId' => $requestId, 'requesterId' => $requesterId);
            }
        }

        return $transactions;
    }

    /**
     * checks if a session id exists and removes it from the user sessions
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse $response
     * 
     * @author maisaraFarahat
     */
    public function logoutAction(\Symfony\Component\HttpFoundation\Request $request) {
        $response = new JsonResponse();
        $badReq = "bad request";
        if (!$request) {
            return new JsonResponse($badReq, 400);
        }
        $sessionId = $request->headers->get("X-SESSION-ID");

        if (!$sessionId) {
            return new JsonResponse($badReq, 400);
        }
        $sessionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if (!$session) {
            return new JsonResponse("the sessionId does not exist", 404);
        }
        if ($session->getExpired()) {
            return new JsonResponse("the sessionId is already expired", 400);
        }
        $user = $session->getUser();

        $user->removeSession($session);

        $session->setExpired(1);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($session);

        $this->getDoctrine()->getManager()->flush();



        return new Response(200);
    }

}
