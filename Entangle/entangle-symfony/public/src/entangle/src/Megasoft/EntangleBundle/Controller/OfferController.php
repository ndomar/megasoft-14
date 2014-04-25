<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller {

    public function changeOfferPriceAction(Request $request, $offerid) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }
        if ($session == null) {
            return new Response("Unauthorized", 401);
        }
        $sessionExpired = $session->getExpired();
        if ($sessionExpired) {
            return new Response("Session expired", 440);
        }
        $offerRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer');
        $requestOffer = $offerRepo->findOneBy(array('id' => $offerid));
        if ($requestOffer == null) {
            return new Response("Not found", 404);
        }
        if (($session->getUserId()) != ($requestOffer->getUserId())) {
            return new Response("Unauthorized", 401);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->ACCEPTED)) {
            return new Response("Offer is already accepted", 401);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->DONE)) {
            return new Response("Offer is already done", 401);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->FAILED)) {
            return new Response("Offer is already failed", 401);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->REJECTED)) {
            return new Response("Offer is already rejected", 401);
        }
        $tangleRequest = $requestOffer->getRequest();
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $newOfferPrice = $json_array['newPrice'];
        if ($newOfferPrice == null) {
            return new Response("Bad Request", 400);
        }
        if (($requestOffer->getRequestedPrice()) == $newOfferPrice) {
            return new Response("Same price, enter a new one", 400);
        }
        $requestOffer->setRequestedPrice($newOfferPrice);
        $this->getDoctrine()->getManager()->persist($requestOffer);
        $this->getDoctrine()->getManager()->flush();
        return new Response('Price changed', 200);
    }

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
        if ($sessionId == null) {
            return $response = new Response("No Session Id.", 400);
        }
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            return $response = new Response("Error: Incorrect Session Id.", 400);
        }
        if ($session->getExpired() == 1) {
            return $response = new Response("Error: Session Expired.", 401);
        }
        $userOfSession = $session->getUserId();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        if ($offerId == null) {
            return $response = new Response("Error: No offer selected.", 400);
        }
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($offer == null) {
            return $response = new Response("Error: No such offer.", 404);
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        $requesterId = $request->getUserId();
        $tangle = $request->getTangleId();
        if ($requesterId != $userOfSession) {
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
        $tangleId = $request->getTangleId();
        $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requester = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $requesterId));
        if (count($requester) <= 0) {
            return "Error: You don't belong to this tangle.";
        }
        if ($request->getDeleted() == 1) {
            return "Error: Request deleted.";
        }
        if ($request->getStatus() == $request->CLOSE) {
            return "Error: Request Closed.";
        }
        if ($request->getStatus() == $request->FROZEN) {
            return "Error: Request is Frozen.";
        }
        if ($offer->getDeleted() == 1) {
            return "Error: Offer deleted.";
        }
        if ($offer->getStatus() == $offer->DONE || $offer->getStatus() == $offer->ACCEPTED) {
            return "Error: Offer has already been accepted.";
        }
        if ($offer->getStatus() == $offer->FAILED || $offer->getStatus() == $offer->REJECTED) {
            return "Error: Offer closed.";
        }


        $price = $offer->getRequestedPrice();

        $requesterBalance = $requester->getCredit();
        if ($requesterBalance < $price) {
            return "Error: Not enough balance.";
        }
        $request->setStatus($request->FROZEN);
        $requester->setCredit($requesterBalance - $price);
        $offer->setStatus(1);
        $doctrine->getManager()->persist($request);
        $doctrine->getManager()->persist($requester);
        $doctrine->getManager()->persist($offer);
        $doctrine->getManager()->flush();
        return "Offer Accepted.";
    }

}
