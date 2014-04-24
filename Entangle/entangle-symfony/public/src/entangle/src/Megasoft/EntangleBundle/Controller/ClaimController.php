<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClaimController extends Controller {

    /**
     * This function gets the emails of both the claimer and the tangle owner from 
     * the data base after making sure of the validation of all the information
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $requestId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author Salma Amr
     */
    public function getMailsAction(\Symfony\Component\HttpFoundation\Request $request, $requestId) {

        if ($requestId == null) {
            return new Response('No such request', 400);
        }
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));

        if ($claimerRequest == null) {
            return new Response('No such claimer', 400);
        }
        $tangleId = $claimerRequest->getTangleId();
        
        if ($sessionId == null) {
            return new Response('No such session', 400);
        }
        
        if ($tangleId == null) {
            return new Response('No such tangle', 400);
        }

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Expired session', 400);
        }
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if ($tangle == null) {
            return new Response('No such tangle', 400);
        }
        $ownerBoolean = true;
        $userId = $session->getUserId();
        $tangleOwnerId = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'tangleOwner' => $ownerBoolean));
        
        if ($userId == null || $tangleOwnerId == null) {
            return new Response('No such user', 400);
        }
        $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $claimerMail = $userRepo->findOneBy(array('userId' => $userId))->getEmail();
        $tangleOwnerMail = $tangleOwnerId;
##$userRepo->findOneBy(array('userId' => $tangleOwnerId))->getEmail();
        $response = new JsonResponse();
        $response->setData
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
    public function createClaimAction(\Symfony\Component\HttpFoundation\Request $request, $requestId) {

        if ($requestId == null) {
            return new Response('No such request', 400);
        }
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));

        if ($claimerRequest == null) {
            return new Response('No such claim', 400);
        }
        $tangleId = $claimerRequest->getTangleId();

        if ($tangleId == null || $sessionId == null) {
            return new Response('either tangle id or session id is null', 400);
        }

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response('session expired', 400);
        }

        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if ($tangle == null) {
            return new Response('No such tangle', 400);
        }
        
        $jsonString = $request->getContent();
        $json_array = json_decode($jsonString, true);
        $mssgBody = $json_array['X-MSSGBODY'];
        
        $userId = $session->getUserId();
        $user = $session->getUser();
        if ($mssgBody == null) {
            return new Response('Empty MssgBody', 400);
        }
        $claim = new Claim();
        $claim->setUser($user);
        $claim->setUserId($userId);
        $claim->setTangle($tangle);
        $claim->setTangleId($tangleId);
        $claim->setMessage($mssgBody);
        $claim->setDeleted(false);
        $response = new JsonResponse();
        $response->setData(array('X-CLAIM-ID' => $claim->getId()));
        $response->setStatusCode(200);
        return $response;
    }
}
