<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Megasoft\EntangleBundle\Entity\Transaction;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Megasoft\EntangleBundle\Entity\UserEmail;

/**
 * Gets the required information to view a certain user's profile
 * @author almgohar
 */
class UserController extends Controller {

    /**
     * This Method edits all user information
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author menna
     */
    public function editAction(Request $request) {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if (!$currentSession) {
            return new Response("Invalid Session Id", 400);
        }
        if (!$currentSession->getExpired()) {
            $user = $currentSession->getUser();
            if ($user) {
                $newDescription = $jsonArray['description'];
                if ($user->getUserBio() != $newDescription) {
                    $user->setUserBio($newDescription);
                }
                $newPassword = $jsonArray['new_password'];
                $confirmNewPassword = $jsonArray['confirm_password'];
                $givenCurrentPassword = $jsonArray['current_password'];
                $currentPassword = $user->getPassword();
                if (($givenCurrentPassword == $currentPassword) && ($newPassword == $confirmNewPassword)) {
                    $user->setPassword(md5($newPassword));
                }
                $newDateOfBirthString = $jsonArray['date_of_birth'];

                if ($newDateOfBirthString) {
                    $newDateOfBirth = strtotime($newDateOfBirthString);
                    $dateGiven = $user->getBirthDate();
                    if ($newDateOfBirth != $dateGiven) {
                        $user->setBirthDate($newDateOfBirth);
                    }
                }
                $doctrineManger = $this->getDoctrine()->getManager();
                if (filter_var($jsonArray['added_email'], FILTER_VALIDATE_EMAIL)) {
                    $newMail = new UserEmail();
                    $newMail->setEmail($jsonArray['added_email']);
                    $newMail->setUser($user);
                    if ($user->getId()) {
                        $newMail->setUserId($user->getId());
                        $user->addEmail($newMail);
                        $doctrineManger->persist($newMail);
                    }
                }
                $user->setAcceptMailNotifications($jsonArray('notification_state'));
                $doctrineManger->persist($user);
                $doctrineManger->flush();
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
    public function deleteSecondaryEmailAction(Request $request) {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($currentSession) {
            if (!$currentSession->getExpired()) {
                $user = $currentSession->getUser();
                $deletedMail = $jsonArray['deletedMail'];
                $user->removeEmail($deletedMail);
                $doctrineManger = $this->getDoctrine()->getManager();
            } else {
                return new Response("Session Expired", 400);
            }
        } else {
            return new Response("Invalid Session Id", 400);
        }
        $doctrineManger->persist($user);
        $doctrineManger->flush();
        return new Response('OK', 200);
    }

    /**
     * This method gets all the user info to be displayed in the frontend 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author menna
     */
    public function retrieveDataAction(Request $request) {
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
        $response = new JsonResponse();
        $response->setData(array('description' => $user->getUserBio(), 'date_of_birth' => $user->getBirthDate()
            , 'notification_state' => $user->getNotificationState()));
        $response->getStatusCode(200);
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
     * Sends the required information in a JSon response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $userId
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author Almgohar
     */
    public function profileAction(Request $request, $userId, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($sessionId == null) {
            return new Response('Unauthorized', 401);
        }

        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        $user = $userTable->findOneBy(array('id' => $userId));

        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
        }

        if ($user == null) {
            return new Response('User not found', 404);
        }

        $loggedInUser = $session->getUserId();

        if (!$this->validateTangle($tangleId)) {
            return new Response('Tangle not found', 404);
        }

        if (!$this->validateUser($loggedInUser, $tangleId)) {
            return new Response('You are not a member of this tangle', 401);
        }

        if (!$this->validateUser($userId, $tangleId)) {
            return new Response('The requested user is not a member of this tangle', 401);
        }

        $offers = $user->getOffers();
        $info = $this->getUserInfo($user, $tangleId);
        $transactions = $this->getTransactions($offers, $tangleId);
        $response = new JsonResponse();
        $response->setData(array('information' => $info,
            'transactions' => $transactions));
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
    private function getTransactions($offers, $tangleId) {
        $transactions = array();
        for ($i = 0; $i < count($offers); $i++) {
            $offer = $offers[$i];
            if ($offer == null) {
                continue;
            }
            if (($offer->getRequest()->getTangleId() == $tangleId) && ($offer->getTransaction() != null)) {
                $requesterName = $offer->getRequest()->getUser()->getName();
                $requestDescription = $offer->getRequest()->getDescription();
                $amount = $offer->getTransaction()->getFinalPrice();
                $transactions[] = array('offerId' => $offer->getId(),
                    'requesterName' => $requesterName,
                    'requestDescription' => $requestDescription,
                    'amount' => $amount);
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
    private function getUserInfo($user, $tangleId) {
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
            'credit' => $credit, 'photo' => $photo, 'birthdate' => $birthdate,
            'verified' => $verfied);
        return $info;
    }

}
