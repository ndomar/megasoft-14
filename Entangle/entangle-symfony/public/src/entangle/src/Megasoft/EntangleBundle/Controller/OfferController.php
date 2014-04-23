<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OfferController extends Controller {

    /**
     * this recieves a request and calls verify to check if it can accept the offer  
     * @param  Request $request
     * @return Response $response returns 201 or 409 status code and message depending on verification
     * @author sak9
     */
    public function acceptOfferAction(Request $request) {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $sessionId = $request->headers->get('X-SESSION-ID');
        if($sessionId==null){
            return $response = new Response("No Session Id.", 400);
        }
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if($session==null){
            return $response = new Response("Error: Incorrect Session Id.", 400);
        }
        if($session->getExpired()==1){
            return $response = new Response("Error: Session Expired.", 401);
        }
        $userOfSession = $session->getUserId();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        if($offerId==null){
            return $response = new Response("Error: No offer selected.", 400);
        }
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if($offer==null){
            return $response = new Response("Error: No such offer.", 404); 
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        $requesterId = $request->getUserId();
        $tangle = $request->getTangleId();
        if($requesterId != $userOfSession){
            return $response = new Response("Error: You are unauthorized to accept this offer.", 409);
        }
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requestTangle = $userTangleRepo->findOneBy(array('userId' => $requesterId,'tangleId'=>'tangle'));
        if(count($requestTangle)<=0){
            return $response = new Response("Error: You are unauthorized to accept this offer.", 409);
        }
        $verificationMessage = $this->verify($offerId);
        if ($verificationMessage == "Offer Accepted.") {
            $response = new Response($verificationMessage, 201);
        } else {
            $response = new Response($verificationMessage, 401);
        }
        return $response;
    }

    /**
     * this recieves an offerId and checks if it can be accepted, if it can it accepts it and updates all fields in tables
     * @param  Int $offerId 
     * @return String either a success or error message
     * @author sak9
     */
    public function verify($offerId) {
        $doctrine = $this->getDoctrine();
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if (count($offer) <= 0) {
            return "Error: No such offer.";
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        $requesterId = $request->getUserId();
        if ($request->getDeleted() == 1) {
            return "Error: Request deleted.";
        }
        if ($request->getStatus() === 1) {
            return "Error: Request Closed.";
        }
        if ($offer->getDeleted() == 1) {
            return "Error: Offer deleted.";
        }
        if ($offer->getStatus() === 1) {
            return "Error: Offer Closed.";
        }
        $tangleId = $request->getTangleId();
        $price = $offer->getRequestedPrice();
        $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requester = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $requesterId));
        $requesterBalance = $requester->getCredit();
        if ($requesterBalance < $price) {
            return "Error: Not enough balance.";
        }
        $request->setStatus(1);
        $requester->setCredit($requesterBalance - $price);
        $offer->setStatus(1);
        $doctrine->getManager()->persist($request);
        $doctrine->getManager()->persist($requester);
        $doctrine->getManager()->persist($offer);
        $doctrine->getManager()->flush();
        return "Offer Accepted.";
    }

}
