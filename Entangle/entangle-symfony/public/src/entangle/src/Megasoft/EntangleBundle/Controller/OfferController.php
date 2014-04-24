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
        $sessionExpired = $session->getExpired();
        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }
        if ($session == null) {
            return new Response("Unauthorized", 401);
        }
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
        if(($requestOffer->getStatus()) == (\Megasoft\EntangleBundle\Entity\Offer::ACCEPTED)){
            return new Response("Offer is already accepted", 401);
        }
        $tangleRequest = $requestOffer->getRequest();
        if ($tangleRequest == null) {
            return new Response("Bad Request", 400);
        }
        if(($tangleRequest->getId()) != ($requestOffer->getRequestId())){
            return new Response("Bad Request", 400);
        }
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $newOfferPrice = $json_array['newPrice'];
        if($newOfferPrice == null){
            return new Response("Bad Request", 400);
        }
        if(($requestOffer->getRequestedPrice()) == $newOfferPrice){
            return new Response("Same price, enter a new one", 400);
        }
        $requestOffer->setRequestedPrice($newOfferPrice);
        $this->getDoctrine()->getManager()->persist($requestOffer);
        $this->getDoctrine()->getManager()->flush();
        return new Response('Price changed', 200);
    }

}
