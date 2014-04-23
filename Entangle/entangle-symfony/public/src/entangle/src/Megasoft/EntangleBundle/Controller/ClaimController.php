<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Claim extends Controller {

    /**
     * This function gets the emails of both the claimer and the tangle owner from 
     * the data base after making sure of the validation of all the information
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $requestId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author Salma Amr
     */
    public function getMails($request, $requestId) {

        if ($requestId == null) {
            return new Response('Bad Request', 400);
        }
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));

        if ($claimerRequest == null) {
            return new Response('Bad Request', 400);
        }
        $tangleId = $claimerRequest->getTangleId();

        if ($tangleId == null || $sessionId == null) {
            return new Response('Bad Request', 400);
        }

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if ($tangle == null) {
            return new Response('Bad Request', 400);
        }
        $userId = $session->getUserId();
        $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $claimerMail = $userRepo->findOneBy(array('userId' => $userId))->getEmail();
        $tangleOwnerId = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'tangleOwner' => 'true'));
        $tangleOwnerMail = $userRepo->findOneBy(array('userId' => $tangleOwnerId))->getEmail();
        $response = new JsonResponse();
        $response->setJsonContent
                (array('X-TANGLEOWNER-MAIL' => $tangleOwnerMail, 'X-CLAIMER-MAIL' => $claimerMail));
        $response->setStatusCode(200);
        return $response;
    }

    /**
     * This function fetches the data attributes of the claim and pass it to the sendClaim function
     * aftr making sure  of the validation of all the info
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $requestId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Salma Amr
     */
    public function createClaim($request, $requestId) {

        if ($requestId == null) {
            return new Response('Bad Request', 400);
        }
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));

        if ($claimerRequest == null) {
            return new Response('Bad Request', 400);
        }
        $tangleId = $claimerRequest->getTangleId();

        if ($tangleId == null || $sessionId == null) {
            return new Response('Bad Request', 400);
        }

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }

        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if ($tangle == null) {
            return new Response('Bad Request', 400);
        }
        $mssgBody = $request->body->get('X-MSSGBODY');
        $userId = $session->getUserId();
        $user = $session->getUser();
        if ($mssgBody == null) {
            return new Response('Empty MssgBody', 400);
        }
        sendClaim($user, $userId, $tangle, $tangleId, $mssgBody);
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
