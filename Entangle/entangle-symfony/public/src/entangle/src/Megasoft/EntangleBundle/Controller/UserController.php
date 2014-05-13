<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Transaction;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class UserController extends Controller
{

    /**
     * Validates the username and password from request and returns sessionID
     * @param  Integer $len length for the generated sessionID
     * @return String $generatedSessionID the session id that will be
     *
     * @author maisaraFarahat
     */
    private function generateSessionId($len)
    {
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
    public function loginAction(\Symfony\Component\HttpFoundation\Request $request)
    {
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
        $deviceType = $json_array['deviceType'];

        if (!$name) {
            return new JsonResponse("missing name", 400);
        }
        if (!$password) {
            return new JsonResponse("missing password", 400);
        }
        if(!$deviceType){
            return new JsonResponse("missing device type", 400);
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
        $session->setDeviceType($deviceType);

        $user->addSession($session);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($session);

        $this->getDoctrine()->getManager()->flush();

        $kernel = $this->get('kernel');
        $filepath = 'http://entangle.io/images/profilePictures/';

        $response->setData(array('sessionId' => $sessionId, 'userId' => $user->getId()
        , 'profileImage' => $filepath . $user->getPhoto(),
            'username' => $user->getName(),));
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
    private function validateUser($userId, $tangleId)
    {
        $userTangleTable = $this->getDoctrine()->
            getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleTable->
                findOneBy(array('userId' => $userId, 'tangleId' => $tangleId,));

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
    private function validateTangle($tangleId)
    {
        $tangleTable = $this->getDoctrine()->
            getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleTable->findOneBy(array('id' => $tangleId,));

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
        $user = $userTable->findOneBy(array('id' => $userId,));

        if ($user == null) {
            return new Response('User not found', 404);
        }

        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId,));
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
     * @param boolean $general
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
        $verified = $user->getVerified();
        $information = array('name' => $name, 'description' => $description,
            'photo' => $photo,
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

        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
        }

        $loggedInUser = $session->getUser();
        $user = $userTable->findOneBy(array('id' => $userId,));
        $userTangle = $userTangleTable->findOneBy(array('userId' => $userId, 'tangleId' => $tangleId,));

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
                $photo = $offer->getRequest()->getUser()->getPhoto();
                $offererName = $offer->getUser()->getName();
                $amount = $offer->getTransaction()->getFinalPrice();
                $requestId = $offer->getRequest()->getId();
                $requesterId = $offer->getRequest()->getUserId();
                $transactions[] = array('offerId'=>$offer->getId(),
                    'requesterName' => $requesterName, 'photo' => $photo, 'offererName' => $offererName,
                    'amount' => $amount, 'requestId' => $requestId, 'requesterId' => $requesterId,);
            } else {
                continue;
            }
        }
        $response = new JsonResponse();
        $response->setData(array('transactions' => $transactions, 'credit' => $credit,));
        $response->setStatusCode(200);

        return $transactions;
    }

    /**
     * Gets the basic information of a given user in a give tangle
     * @param user $user
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response | array #info
     * @author Almgohar
     */
    private function getUserInfo($user, $tangleId)
    {
        if ($user == null) {
            return new Response('Bad Request', 400);
        }
        $doctrine = $this->getDoctrine();
        $userId = $user->getId();
        $userTangleTable = $doctrine->
            getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleTable->
            findOneBy(array('userId' => $userId, 'tangleId' => $tangleId));
        $name = $user->getName();
        $description = $user->getUserBio();
        $credit = $userTangle->getCredit();
        $photo = $user->getPhoto();
        $birthdate = $user->getBirthDate();
        $verfied = $user->getVerified();
        $info = array('name' => $name, 'description' => $description,
            'credit' => $credit, 'photo' => 'http://entangle.io/images/profilePictures/' . $photo, 'birthdate' => $birthdate,
            'verified' => $verfied);

        return $info;
    }

    /**
     * checks if a session id exists and removes it from the user sessions
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse $response
     *
     * @author maisaraFarahat
     */
    public function logoutAction(\Symfony\Component\HttpFoundation\Request $request)
    {
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

    /*
     * Checks that the username is unique
     * @param: String  username
     * @param: boolean value, true if unique. False otherwise
     * 
     * @author: Eslam
     */

    private function validateUniqueUsername($username) {
        $userRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        if ($userRepo->findOneBy(array('name' => $username,)) == null && $username != null && $username != "") {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Checks that the email is unique
     * @param: String email
     * @param: boolean value, true if unique. False otherwise
     * 
     * @author: Eslam
     */

    private function validateUniqueEmail($email) {
        $emailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        if ($emailRepo->findOneBy(array('email' => $email,))) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * An endpoint for the user to register from the mobile application
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse $response
     *
     * @author Eslam Maged
     */

    public function registerAction(\Symfony\Component\HttpFoundation\Request $request) {
        $response = new JsonResponse();
        $badRequest = "Bad Request";

        if (!$request) {
            return new JsonResponse($badRequest, 400);
        }

        $json = $request->getContent();

        if (!$json) {
            $response->setStatusCode(400, $badRequest);
            return $response;
        }

        if ($request->getMethod() == 'POST') {
            $json_array = json_decode($json, true);
            $username = $json_array['username'];
            $email = $json_array['email'];
            $password = $json_array['password'];
            $confirmPassword = $json_array['confirmPassword'];

            if (!$username || !$email || !$password || !$confirmPassword || (!preg_match('/^[a-zA-Z0-9]+$/', $username))) {
                return new JsonResponse($badRequest, 400);
            }

            if(!$this->validateUniqueUsername($username)) {
                return new JsonResponse("Not unique username", 401);
            }

            if(!$this->validateUniqueEmail($email)) {
                return new JsonResponse("Not unique Email", 402);
            }


            $user = new User;
            $userEmail = new UserEmail();
            $user->addEmail($userEmail);
            $user->setName($username);
            $user->setPassword($password);
            $userEmail->setEmail($email);
            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->persist($user);
            $entityManager->persist($userEmail);
            $entityManager->flush();
            $response->setData(array('username' => $username, 'email' => $email, 'password' => $password,));
            $response->setStatusCode(201);

            return $response;
        }
    }

}
