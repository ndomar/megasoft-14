<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Claim extends Controller {

    /**
     * This function validates the info of the coming request
     * @param http request $request
     * @param int $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|boolean
     * @author Salma Amr
     */
    public function validateRequest($request, $tangleId) {
        $flag = true;
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($tangleId == null || $sessionId == null) {
            $flag = false;
            return new Response('Bad Request', 400);
        }
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            $flag = false;
            return new Response('Bad Request', 400);
        }
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if ($tangle == null) {
            $flag = false;
            return new Response('Bad Request', 400);
        }
        return $flag;
    }

    /**
     * This function gets the emails of both the offerer and the tangle owner from 
     * the data base by matching with the tangleid and the session id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $tangleId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author Salma Amr
     */
    public function getMails($request, $tangleId) {
        $flag = validateRequest($request, $tangleId);
        if ($flag == true) {
            $sessionId = $request->headers->get('X-SESSION-ID');
            $doctrine = $this->getDoctrine();
            $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
            $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
            $userId = $session->getUserId();
            $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
            $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
            $offererMail = $userRepo->findOneBy(array('userId' => $userId))->getEmail();
            $tangleOwnerId = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'tangleOwner' => 'true'));
            $tangleOwnerMail = $userRepo->findOneBy(array('userId' => $tangleOwnerId))->getEmail();
            $response = new JsonResponse();
            $response->setJsonContent
                    (array('X-TANGLEOWNER-MAIL' => $tangleOwnerMail, 'X-OFFERER-MAIL' => $offererMail));
            $response->setStatusCode(200);
            return $response;
        }
    }

    /**
     * This function fetches the data attributes of the claim and pass it to the sendClaim function
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $tangleId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Salma Amr
     */
    public function createClaim($request, $tangleId) {

        $flag = validateRequest($request, $tangleId);
        if ($flag == true) {
            $sessionId = $request->headers->get('X-SESSION-ID');
            $subject = $request->body->get('X-SUBJECT');
            $mssgBody = $request->body->get('X-MSSGBODY');
            $doctrine = $this->getDoctrine();
            $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
            $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
            $userId = $session->getUserId();
            $user = $session->getUser();
            $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
            $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
            if ($mssgBody == null) {
                return new Response('Empty MssgBody', 400);
            }
            sendClaim($user, $userId, $tangle, $tangleId, $mssgBody);
        }
    }

    /**
     * This function creates the claim by setting the attributes to the newly created claim
     * @param user $user
     * @param String $userId
     * @param tangle $tangle
     * @param int $tangleId
     * @param String $mssgBody
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author Salma Amr
     */
    public function sendClaim($user, $userId, $tangle, $tangleId, $mssgBody) {
        $claim = new Claim();
        $claim->setUser($user);
        $claim->setUserId($userId);
        $claim->setTangle($tangle);
        $claim->setTangleId($tangleId);
        $claim->setMessage($mssgBody);
        $claim->setDeleted('false');
        $response = new JsonResponse();
        $response->setJsonContent(array('X-CLAIM-ID' => $claim->getId()));
        $response->setStatusCode(200);
        return $response;
    }

}
