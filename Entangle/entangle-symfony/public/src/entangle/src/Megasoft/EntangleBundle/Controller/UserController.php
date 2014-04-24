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

/**
 * Gets the required information to view a certain user's profile
 * @author almgohar
 */
class UserController extends Controller {

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
        $user = $session->getUser();

        $user->removeSession($session);

        $session->setExpired(1);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($session);

        $this->getDoctrine()->getManager()->flush();
//        $response->setData(array('sessionId' => $sessionId));

        $response->setStatusCode(200);

        return $response;
    }

}
