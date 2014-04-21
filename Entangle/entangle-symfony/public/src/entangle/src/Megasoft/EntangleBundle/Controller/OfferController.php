<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller {

    public function changeOfferPriceAction(Request $request, $requestid, $offerid) {
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
        $requestRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $tangleRequest = $requestRepo->findOneBy(array('id' => $requestid));
        if ($tangleRequest == null) {
            return new Response("Not found", 404);
        }
        $offerRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer');
        $requestOffer = $offerRepo->findOneBy(array('id' => $offerid));
        if ($requestOffer == null) {
            return new Response("Not found", 404);
        }
        if (($requestOffer->getRequestId()) != $requestid) {
            return new Response("Unauthorized", 401);
        }
        if (($session->getUserId()) != ($requestOffer->getUserId())) {
            return new Response("Unauthorized", 401);
        }
        if(($requestOffer->getStatus) == (\Megasoft\EntangleBundle\Entity\Offer::ACCEPTED)){
            return new Response("Offer is already accepted", 401);
        }
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $newOfferPrice = $json_array['newPrice'];
        $requestOffer->setRequestedPrice($newOfferPrice);
        return new Response('Price changed', 200);
    }

}
