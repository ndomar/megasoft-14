<?php

namespace Megasoft\EntangleBundle\Controller;

use DateTime as DateTime2;
use Megasoft\EntangleBundle\Entity\Transaction;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class UserController extends Controller
{

    /**
     * This Method edits all user information
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author menna
     */
    public function editAction(Request $request)
    {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if (!$currentSession) {
            return new Response("Invalid Session Id", 400);
        }
        if ($currentSession->getExpired() == 0) {
            $user = $currentSession->getUser();

            if ($user != null) {
                $newDescription = $jsonArray['description'];
                if ($user->getUserBio() != $newDescription) {
                    $user->setUserBio($newDescription);
                }
                $oldDate = $user->getBirthDate();
                $newDateOfBirth = $jsonArray['new_date_of_birth'];
                $birthDate = new DateTime2($newDateOfBirth);
                if ($birthDate != $oldDate) {
                    $user->setBirthDate($birthDate);
                }
                $doctrineManger = $this->getDoctrine()->getManager();
                $email_array = $jsonArray['emails'];
                // hena nebda2 el habal :D by maisara isA :D :P

                if (!empty($email_array)) {
                    foreach ($email_array as $email) {
                        $repo = $this->getDoctrine()->getManager()->getRepository('MegasoftEntangleBundle:UserEmail');
                        $emailExistsNotDeleted = $repo->findOneBy(array('email' => $email, 'deleted' => 0,));
                        if ($emailExistsNotDeleted)
                            continue;
                        else {
                            $emailExistsDeleted = $repo->findOneBy(array('email' => $email, 'deleted' => 1,));
                            if ($emailExistsDeleted) {
                                if ($emailExistsDeleted->getUserId() == $user->getId())
                                    $emailExistsDeleted->setDeleted(0);
                                else {
                                    $emailExistsDeleted->setDeleted(0);
                                    $emailExistsDeleted->setUserId($user->getId());
                                }
                            } else
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $newMail = new UserEmail();
                                    $newMail->setEmail($email);
                                    $newMail->setUser($user);
                                    if ($user->getId()) {
                                        $newMail->setUserId($user->getId());
                                        $user->addEmail($newMail);
                                        $doctrineManger->persist($newMail);
                                    }
                                }
                        }
                    }
                }
                $user->setAcceptMailNotifications($jsonArray['notification_state']);
                $doctrineManger->persist($user);
                $doctrineManger->flush();

                // now i will do the whole code for deleting the email from the current emails isA
                // pray for me if you see this :D :P ;) maisara

                $userid = $user->getId();
                echo $userid;
                $repo = $this->getDoctrine()->getManager()->getRepository('MegasoftEntangleBundle:UserEmail');
                $currentUserEmails = $repo->findBy(array('userId' => $userid));
                foreach ($currentUserEmails as $email) {
                    $found = 0;

                    foreach ($email_array as $newEmails) {
                        if ($email->getEmail() == $newEmails) {
                            echo 'ana gowa el codition';
                            $found = 1;
                        }
                    }
                    if ($found == 0) {
                        $email->setDeleted(1);
                        $this->getDoctrine()->getManager()->persist($email);
                    }
                }

                $this->getDoctrine()->getManager()->flush();
                echo 'ana ba3d el flush';
                return new Response('OK', 200);
            }
        } else {
            return new Response("Session Expired", 400);
        }
    }

    /**
     * This Method Deletes Secondary Emails of the user
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author menna
     */
    public function deleteSecondaryEmailAction(Request $request)
    {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($currentSession) {
            if (!$currentSession->getExpired()) {
                $user = $currentSession->getUser();
                $deletedMail = $jsonArray['deleted_mail'];
                $user->removeEmail($deletedMail);
                $doctrineManger = $this->getDoctrine()->getManager();
                $doctrineManger->persist($user);
                $doctrineManger->flush();
                return new Response('OK', 200);
            } else {
                return new Response("Session Expired", 400);
            }
        } else {
            return new Response("Invalid Session Id", 400);
        }
    }

    /**
     * This method getsall the user info to be displayed in the frontend
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author menna
     */
    public function retrieveDataAction(Request $request)
    {
        $userEmailArray = array();
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if (!$currentSession) {
            return new Response("Invalid Session Id", 400);
        }
        if ($currentSession->getExpired()) {
            return new Response("Session Expired", 400);
        }
        $user = $currentSession->getUser();
        $emails_array = $user->getEmails();
        $userId = $user->getId();
        foreach ($emails_array as $user_email) {
            if ($user_email->getDeleted() == 0)
                array_push($userEmailArray, $user_email->getEmail());
        }
        $response = new JsonResponse();
        $response->setData(array('description' => $user->getUserBio(), 'date_of_birth' => $user->getBirthDate()
        , 'notification_state' => $user->getAcceptMailNotifications(), 'emails' => $userEmailArray, 'userId' => $userId,));
        $response->setStatusCode(200);
        return $response;
    }

    /* Validates the username and password from request and returns sessionID
     * @param  Integer $len length for the generated sessionID
     * @return String $generatedSessionID the session id that will be config.php – This file contains constant v used
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
        if (!$deviceType) {
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
    public function generalProfileAction(\Symfony\Component\HttpFoundation\Request $request, $userId)
    {
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
    public function profileAction(\Symfony\Component\HttpFoundation\Request $request, $userId, $tangleId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($sessionId == null) {
            return new Response('Unauthorized', 401);
        }

        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user = $userTable->findOneBy(array('id' => $userId,));
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId,));

        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
        }

        $loggedInUser = $session->getUser();

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
    private function viewProfile($user)
    {
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
    public function transactionsAction(\Symfony\Component\HttpFoundation\Request $request, $userId, $tangleId)
    {
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

        if ($user == null) {
            return new Response('User not found', 404);
        }

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

            if (($offer->getRequest()->getTangleId() == $tangleId) && ($offer->getTransaction() != null)
                && !($offer->getTransaction()->getDeleted())
            ) {
                $requesterName = $offer->getRequest()->getUser()->getName();
                $photo = $offer->getRequest()->getUser()->getPhoto();
                $offererName = $offer->getUser()->getName();
                $amount = $offer->getTransaction()->getFinalPrice();

                $requestId = $offer->getRequest()->getId();
                $requesterId = $offer->getRequest()->getUserId();
                $transactions[] = array('offerId' => $offer->getId(),
                    'requesterName' => $requesterName, 'photo' => $photo, 'offererName' => $offererName,
                    'amount' => $amount, 'requestId' => $requestId, 'requesterId' => $requesterId,);
            } else {
                continue;
            }
        }
        $response = new JsonResponse();
        $response->setData(array('transactions' => $transactions, 'credit' => $credit,));
        $response->setStatusCode(200);
        return $response;
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

    private function validateUniqueUsername($username)
    {
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

    private function validateUniqueEmail($email)
    {
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

    public function registerAction(\Symfony\Component\HttpFoundation\Request $request)
    {
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

            if (!$this->validateUniqueUsername($username)) {
                return new JsonResponse("Not unique username", 401);
            }

            if (!$this->validateUniqueEmail($email)) {
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
