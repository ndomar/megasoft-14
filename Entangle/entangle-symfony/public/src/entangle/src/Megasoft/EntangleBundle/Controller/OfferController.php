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

    public function acceptOfferAction(Request $request) {
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        $this->verify($offerId);
        $response = new Response();
        $response->setStatusCode(200);
        return $response;
    }

    public function verify($offerId) {
        $doctrine = $this->getDoctrine();
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($offer->getStatus() === 0) {
            $requestId = $offer->getRequestId();
            $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
            $request = $requestRepo->findOneBy(array('id' => $requestId));
            $requesterId = $request->getUserId();
            if ($request->getStatus() === 0) {
                $tangleId = $request->getTangleId();
                $price = $offer->getRequestedPrice();
                $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
                $requester = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $requesterId));
                $requesterBalance = $requester->getCredit();
                if ($requesterBalance >=$price) {
                    printf("verified");
                    $request->setStatus(1); //check this;
                    $this->getDoctrine()->getManager()->persist($request);
                    $requester->setCredit($requesterBalance - $price);
                    $this->getDoctrine()->getManager()->persist($requester);
                    $offer->setStatus(1);
                    $this->getDoctrine()->getManager()->persist($offer);
                    $this->getDoctrine()->getManager()->flush();
                } else {
                    printf("requester doesn't have enough balance");
                }
            } else {
                printf("request closed");
            }
        } else {
            printf("offer closed");
        }
    }

}
