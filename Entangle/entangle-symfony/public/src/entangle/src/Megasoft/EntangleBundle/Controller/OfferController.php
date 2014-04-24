<?php
namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/* 
 * Class RegisterController
 * @package Megasoft\EntangleBundle\Controller
 * Responsible for retrieving and updating offer status
 * @author: mohamedzayan
 */
Class OfferController extends Controller {
    /**
     * this retrieves details of a certain offer from the database
     * @param  Int $offerid  offer ID
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author mohamedzayan
     */
    public function searchAction($offerid) {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offerId = $offerid;
        $offer = $repo->find($offerId);
        $price = $offer->getRequestedPrice();
        $date = $offer->getDate();
        $deadline = $offer->getExpectedDeadline();
        $description = $offer->getDescription();
        $response = new JsonResponse();
        $response->setData(array('price' => $price, 'date' => $date, 'deadline' => $deadline, 'description' => $description));
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * this marks an offer as done if it accepted and not already marked
     * @param  Int $offerid  offer ID
     * @param  \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author mohamedzayan
     */
    public function updateAction($offerid, Request $request) {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $status = $json_array['status'];
        $offerId = $offerid;
        $offer = $repo->find($offerId);
        $backendstatus = $offer->getStatus();
        if ($backendstatus == 2) {
            return new JsonResponse("Offer already marked as done", 401);
        } else if ($backendstatus == 0) {
            return new JsonResponse("Offer is not accepted", 401);
        } else if ($status != 2) {
            return new JsonResponse("Not Allowed", 401);
        } else {
            $offer->setStatus($status);
            $this->getDoctrine()->getManager()->persist($offer);
            $this->getDoctrine()->getManager()->flush();
            $response = new JsonResponse();
            $response->setStatusCode(201);
            return $response;
        }
    }
}