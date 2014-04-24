<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Megasoft\EntangleBundle\Entity\Claim;

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
        if ($session == null) {
            return new Response('No such session', 400);
        }
        if ($session->getExpired()) {
            return new Response('Expired session', 400);
        }
        $tangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleRepo->findOneBy(array('id' => $tangleId, 'deleted' => false));

        if ($tangle == null) {
            return new Response('No such tangle', 400);
        }
        $userId = $session->getUserId();
        $tangleUsers = $tangle->getUserTangles();
        $arrlength = count($tangleUsers);
        
         for ($i = 0; $i < $arrlength; $i++) {
             if (($tangleUsers[$i]->getTangleId() == $tangleId)
                     && ($tangleUsers[$i]->getTangleOwner() == TRUE)) {
                 $tangleOwnerId = $tangleUsers[$i]->getUserId();
                 break;
             }
         }

        if ($tangleOwnerId) {
            return new Response('No such tangle owner', 400);
        }
        if ($userId == null) {
            return new Response('No such claimer', 400);
        }
        $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $claimerMail = $userRepo->findOneBy(array('userId' => $userId))->getEmail();
        $tangleOwnerMail = $userRepo->findOneBy(array('userId' => $tangleOwnerId))->getEmail();
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

        if ($tangleId == null) {
            return new Response('Tangle id is null', 400);
        }
        if ($sessionId == null) {
            return new Response('session id is null', 400);
        }

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response('No such session', 400);
        }
        $tangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleRepo->findOneBy(array('id' => $tangleId, 'deleted' => false));

        if ($tangle == null) {
            return new Response('No such tangle', 400);
        }
        $jsonString = $request->getContent();
        $json_array = json_decode($jsonString, true);
        $mssgBody = $json_array['X-MSSGBODY'];
        $user = $session->getUser();
        $userId = $user->getId();
        if ($mssgBody == null) {
            return new Response('Empty MssgBody', 400);
        }
        $claim = new Claim();
        $claim->setUser($user);
        $claim->setUsedId($userId);
        $claim->setTangle($tangle);
        $claim->setTangleId($tangleId);
        $claim->setMessage($mssgBody);
        $claim->setDeleted(false);
        $claim->setStatus(0);
        $doctrine->getManager()->persist($claim);
        $doctrine->getManager()->flush();
        $response = new JsonResponse();
        $response->setData(array('X-CLAIM-ID' => $claim->getId()));
        $response->setStatusCode(201);
        return $response;
    }

}