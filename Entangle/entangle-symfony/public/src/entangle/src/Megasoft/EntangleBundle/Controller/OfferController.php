<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OfferController
 *
 * @author sak
 */
class OfferController extends Controller {

    /**
     * this recieves a request and calls verify to check if it can accept the offer  
     * @param  Request $request
     * @return Response $response returns 201 or 409 status code and message depending on verification
     * @author sak9
     */
    public function acceptOfferAction(Request $request) {
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        $verificationMessage = $this->verify($offerId);
        if ($verificationMessage == "Offer Accepted.") {
            $response = new Response($verificationMessage, 201);
        } else {
            $response = new Response($verificationMessage, 409);
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
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        $requesterId = $request->getUserId();
        if ($request->getStatus() === 0) {
            if ($offer->getStatus() === 0) {
                $tangleId = $request->getTangleId();
                $price = $offer->getRequestedPrice();
                $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
                $requester = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $requesterId));
                $requesterBalance = $requester->getCredit();
                if ($requesterBalance >= $price) {
                    $request->setStatus(1);
                    $requester->setCredit($requesterBalance - $price);
                    $offer->setStatus(1);
                    $doctrine->getManager()->persist($request);
                    $doctrine->getManager()->persist($requester);
                    $doctrine->getManager()->persist($offer);
                    $doctrine->getManager()->flush();
                    return "Offer Accepted.";
                } else {
                    return "Error: Not enough balance.";
                }
            } else {
                return "Error: Offer Closed.";
            }
        } else {
            return "Error: Request Closed.";
        }
    }

}
