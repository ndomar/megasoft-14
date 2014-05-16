<?php

namespace Megasoft\EntangleBundle\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Megasoft\EntangleBundle\Entity\Claim;
use Megasoft\EntangleBundle\Entity\Transaction;

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
        $validation = $this->validate($request, $requestId);
        if ($validation != null) {
            return $validation;
        }
        $doctrine = $this->getDoctrine();
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));
        $userId = $session->getUserId();
        $tangleId = $claimerRequest->getTangleId();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'tangleOwner' => 1));
        if ($userTangle == null) {
            return new Response('Bad Request', 400);
        }
        $tangleOwnerId = $userTangle->getUserId();
        if ($tangleOwnerId == null) {
            return new Response('No such tangle owner', 400);
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

        $validation = $this->validate($request, $requestId);
        if ($validation != null) {
            return $validation;
        }
        $doctrine = $this->getDoctrine();
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $user = $session->getUser();
        $userId = $user->getId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $tangleId = $claimerRequest->getTangleId();
        $tangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleRepo->findOneBy(array('id' => $tangleId, 'deleted' => false));
        $jsonString = $request->getContent();
        $json_array = json_decode($jsonString, true);
        $mssgBody = $json_array['X-MSSGBODY'];
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

    public function validate($request, $requestId) {

        if ($requestId == null) {
            return new Response('No such request', 400);
        }
        $doctrine = $this->getDoctrine();
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $claimerRequest = $requestRepo->findOneBy(array('id' => $requestId));
        if ($session == null) {
            return new Response('No such session', 400);
        }
        if ($sessionId == null) {
            return new Response('No such session', 400);
        }
        if ($session->getExpired()) {
            return new Response('No such session', 400);
        }
        $userId = $session->getUserId();
        if ($userId == null) {
            return new Response('No such claimer', 400);
        }
        if ($claimerRequest == null) {
            return new Response('No such request', 400);
        }
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('requestId' => $requestId, 'deleted' => false, 'status' => 2));

        if ($offer == null) {
            return new Response('No such offer', 400);
        }
        if (!($offer->getUserId() == $userId || $claimerRequest->getUserId() == $userId)) {
            return new Response('Not authorized to claim', 400);
        }
        $tangleId = $claimerRequest->getTangleId();

        $tangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleRepo->findOneBy(array('id' => $tangleId, 'deleted' => false));

        if ($tangleId == null || $tangle == null) {
            return new Response('No such tangle', 400);
        }

        return null;
    }

    public function resolveClaimAction($claimId, \Symfony\Component\HttpFoundation\Request $request) {
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $sessionMessage= $this->verifySessionId($request);
        $response = new JsonResponse();
        if($sessionMessage == 'error'){
            $response->setContent('Please login again');
            $response->setStatusCode(401);
            return $response;
        }
        $doctrine = $this->getDoctrine();
        
        $tangleId = $json_array['tangleId'];
        $isOwner = $this->verifiyTangleOwner($tangleId, $request);
        if ($isOwner == "error") {
            $response->setContent('You are not authorized to perform this action');
            $response->setStatusCode(400);
            return $response;
        }
        if ($tangleId == null) {
            $response->setConent("Please choose a tangle");
            $response->setStatusCode(400);
            return $response;
        }
        $claimRepo = $doctrine->getRepository('MegasoftEntangleBundle:Claim');
        $claim = $claimRepo->findOneBy(array('id' => $claimId));
        if ($claim == null) {
            $response->setConent("No such claim");
            $response->setStatusCode(400);
            return $response;
        }
        if ($claim->getStatus() == 1) {
            $response->setContent("Claim already resolved");
            $response->setStatusCode(400);
            return $response;
        }
        $tangleId = $claim->getTangleId();
        $offerId = $claim->getOfferId();
        $claimerId = $claim->getClaimerId();
        $deleted = $claim->getDeleted();
        if ($tangleId == null || $offerId == null || $claimerId == null) {
            $response->setConent("Claim is missing data");
            $response->setStatusCode(400);
            return $response;
        }
        if ($deleted == 1) {
            $response->setConent("Claim has been deleted");
            $response->setStatusCode(400);
            return $response;
        }
        $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $claimer = $userRepo->findOneBy(array('id' => $claimerId));
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($claimer == null || $offer == null) {
            $response->setConent("Claimer or offer does not exist");
            $response->setStatusCode(400);
            return $response;
        }
        $offererId = $offer->getUserId();
        if ($offererId == null) {
            $response->setConent("Offerer does not exist");
            $response->setStatusCode(400);
            return $response;
        }
        $requestId = $offer->getRequestId();
        if ($requestId == null) {
            $response->setConent("Request not specified");
            $response->setStatusCode(400);
            return $response;
        }
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        if ($request == null) {
            $response->setConent("Request does not exist");
            $response->setStatusCode(400);
            return $response;
        }
        $requesterId = $request->getUserId();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requesterTangle = $userTangleRepo->findOneBy(array('userId' => $requesterId));
        $offererTangle = $userTangleRepo->findOneBy(array('userId' => $offererId));
        $requesterTangle->setCredit($requesterTangle->getCredit() + $json_array['requesterCredit']);
        $offererTangle->setCredit($offererTangle->getCredit() + $json_array['offererCredit']);
        $claim->setStatus(1);
        $transaction = new Transaction();
        $transaction->setOffer($offer);
        $transaction->setDeleted(0);
        $transaction->setFinalPrice($json_array['offererCredit']);
        $transaction->setDate(new DateTime('NOW'));
        $response->setContent("Claim resolved");
        $response->setStatusCode(200);
        $doctrine->getManager()->persist($requesterTangle);
        $doctrine->getManager()->persist($offererTangle);
        $doctrine->getManager()->persist($claim);
        $doctrine->getManager()->persist($transaction);
        $doctrine->getManager()->flush();
        return $response;
    }

    public function verifySessionId(\Symfony\Component\HttpFoundation\Request $request) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $response = new JsonResponse();
        if ($sessionId == null) {
            
            return 'error';
        }
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired() == 1) {
         
            return 'error';
        }
    }

    public function verifiyTangleOwner($tangleId, \Symfony\Component\HttpFoundation\Request $request) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $sessionUserId = $session->getUserId();
        $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $user = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $sessionUserId));
        if ($user == null) {
            return "error";
        }
        if ($user->getTangleOwner() == 0) {
            return "error";
        }
        return;
    }

    public function getClaimsAction($tangleId, \Symfony\Component\HttpFoundation\Request $request) {
        $this->verifySessionId($request);
        $sessionMessage= $this->verifySessionId($request);
        $response = new JsonResponse();
        if($sessionMessage=='error'){
            $response->setContent('Please login again');
            $response->setStatusCode(401);
            return $response;
        }
        $isOwner = $this->verifiyTangleOwner($tangleId, $request);
       
        if ($isOwner == "error") {
            $response->setContent('You are not authorized to perform this action');
            $response->setStatusCode(401);
            return $response;
        }
        $doctrine = $this->getDoctrine();
        $claimArray = array();
        $claimRepo = $doctrine->getRepository('MegasoftEntangleBundle:Claim');
        $allClaims = $claimRepo->findBy(array('tangleId' => $tangleId, 'status' => 0, 'deleted' => 0));
        $numOfClaims = count($allClaims);
        if ($numOfClaims == 0) {
            $response->setData(array('claims'=>$claimArray));
            $response->setStatusCode(201);
            return $response;
        }
        for ($i = 0; $i < $numOfClaims; $i++) {
            $claimId = $allClaims[$i]->getId();
            $status = $allClaims[$i]->getStatus();
            $message = $allClaims[$i]->getMessage();
            $date = $allClaims[$i]->getCreated();
            $offerId = $allClaims[$i]->getOfferId();
            $claimerId = $allClaims[$i]->getClaimerId();
            $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
            $claimer = $userRepo->findOneBy(array('id' => $claimerId));
            $claimerName = $claimer->getName(); 
            $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
            $offer = $offerRepo->findOneBy(array('id' => $offerId)); 
            $requestId = $offer->getRequestId();
            $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
            $request = $requestRepo->findOneBy(array('id' => $requestId));
            $requesterId = $request->getUserId();
            $requester = $userRepo->findOneBy(array('id' => $requesterId));
            $requesterName = $requester->getName();
            $offererId = $offer->getUserId();
            $offerer = $userRepo->findOneBy(array('id' => $offererId));
            $offererName = $offerer->getName();
            $offerPrice= $offer->getRequestedPrice();
            $details = array('claimerId'=>$claimerId, 'claimId' => $claimId, 'offerId' => $offerId, 
                'offererId' => $offererId,'offererName'=>$offererName, 'requesterId' => $requesterId, 
                'requesterName'=>$requesterName,'offerPrice'=>$offerPrice, 
                'message' => $message, 'status' => $status, 'created' => $date,'claimerName'=>$claimerName);
            array_push($claimArray, $details);
        }
        $response->setData(array('claims'=>$claimArray));
        $response->setStatusCode(200);
        return $response;
    }

}
