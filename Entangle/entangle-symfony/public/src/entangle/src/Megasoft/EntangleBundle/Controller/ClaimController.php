<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Megasoft\EntangleBundle\Entity\Claim;

class ClaimController extends Controller {
/**
 * This function gets the the report required data after the validation of all the data 
 * @param \Symfony\Component\HttpFoundation\Request $request
 * @param int $claimId
 * @param int $offerId
 * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
 * @author Salma Amr
 */
public function claimRenderAction(\Symfony\Component\HttpFoundation\Request $request, $claimId, $offerId) {

if ($claimId == null || $offerId == null) {
return new Response('No such claim or offer', 400);
}
$doctrine = $this->getDoctrine();
$sessionId = $request->headers->get('X-SESSION-ID');
$sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
$session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
if ($sessionId == null || $session == null || $session->getExpired()) {
return new Response('Expired session id', 400);
}
$claimRepo = $doctrine->getRepository('MegasoftEntangleBundle:Claim');
$claim = $claimRepo->findOneBy(array('id' => $claimId));
if ($claim == null) {
return new Response('no such claim', 400);
}
$createdOn = $claim->getCreated();
$claimer = $claim->getClaimer();
$claimerId = $claim->getClaimerId();
$claimMessage = $claim->getMessage();
if ($claimer == null || $claimId == null) {
return new Response('no such claimer', 400);
}
$userRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
$claimerName = $userRepo->findOneBy(array('id' => $claimerId))->getName();
$offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
$offer = $offerRepo->findOneBy(array('id' => $offerId));
if ($offer == null) {
return new Response('no such offer', 400);
}
$offerer = $offer->getUser();
if ($offerer == null) {
return new Response('no such offerer', 400);
}
$offererName = $offerer->getName();
$userEmailRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
$offererEmail = $userEmailRepo->findOneBy(array('user' => $offerer))->getEmail();
$request = $offer->getRequest();
if ($request == null) {
return new Response('no such request', 400);
}
$requester = $request->getUser();
$requesterName = $requester->getName();
$requesterEmail = $userEmailRepo->findOneBy(array('user' => $requester))->getEmail();
$tangleId = $request->getTangleId();
$tangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
If ($tangleId == null) {
return new Response('nu such tangle', 400);
}
$tangleName = $tangleRepo->findOneBy(array('id' => $tangleId))->getName();
$userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
$userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'tangleOwner' => 1));
if ($userTangle == null) {
return new Response('Bad Request', 400);
}
$tangleOwnerId = $userTangle->getUserId();
if ($tangleOwnerId == null) {
return new Response('No such tangle owner', 400);
}
$tangleOwnerName = $userRepo->findOneBy(array('id' => $tangleOwnerId))->getName();
$tangleOwnerMail = $userRepo->findOneBy(array('id' => $tangleOwnerId))->getEmail();
$response = new JsonResponse();
$response->setData
(array('X-CLAIM-DATE' =>$createdOn ,'X-CLAIMER' => $claimerName, 'X-OFFERER' => $offererName,
 'X-OFFERER-EMAIL' => $offererEmail, 'X-REQUESTER' => $requesterName, 'X-REQUESTER-EMAIL' => $requesterEmail,
 'X-TANGLE-OWNER' => $tangleOwnerName,'X-TANGLE-OWNER-EMAIL' => $tangleOwnerMail,  'X-TANGLE' => $tangleName,
 'X-CLAIM-MESSAGE' => $claimMessage));
$response->setStatusCode(200);

return $response;
}
    /**
     * This function fetches the data attributes of the claim and pass it to the sendClaim function
     * aftr making sure  of the validation of all the info
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $requestId
     * @param int $offerId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Salma Amr
     */
    public function createClaimAction(\Symfony\Component\HttpFoundation\Request $request, $requestId, $offerId) {

        $validation = $this->validate($request, $requestId, $offerId);
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
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
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
        $claim->setCreated(new \DateTime("now"));
        $claim->setClaimer($user);
        $claim->setClaimerId($userId);
        $claim->setTangle($tangle);
        $claim->setTangleId($tangleId);
        $claim->setOffer($offer);
        $claim->setOfferId($offerId);
        $claim->setMessage($mssgBody);
        $claim->setDeleted(false);
        $claim->setStatus(0);
        $doctrine->getManager()->persist($claim);
        $doctrine->getManager()->flush();
        $tangle->addClaim($claim);
        $response = new JsonResponse();
        $response->setData(array('X-CLAIM-ID' => $claim->getId()));
        $response->setStatusCode(201);

        return $response;
    }

    /**
     * This function validates the information of the offer and the request to create the claim
     * @param type $request
     * @param type $requestId
     * @param type $offerId
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function validate($request, $requestId, $offerId) {

        if ($requestId == null || $offerId == null) {
            return new Response('No such request or offer', 400);
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

}