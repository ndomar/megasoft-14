<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ShowRequestController extends Controller {

    /**
     * this returns a response depending on the size of the array it recieved from getRequestDetails 
     * @param  Int $requestId  Request id
     * @return Response 
     * @author sak93
     */
    public function getRequestAttibutesAction($requestId) {
        $requestDetails = $this->getRequestDetails($requestId);
        if (count($requestDetails) == 0) {
            return new Response("No such request.", 404);
        } else {
            $response = new JsonResponse();
            $response->setData(array($requestDetails));
            $response->setStatusCode(200);
            return $response;
        }
    }

    /**
     * this method makes an array of all the request details
     * @param  Int $requestId  Request id
     * @return Array $requestDetails 
     * @author sak93
     */
    public function getRequestDetails($requestId) {
        $repository = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $request = $repository->findOneBy(array('id' => $requestId));
        $requestDetails = array();
        if (count($request) != 0) {
            $requester = $request->getUserId();
            $description = $request->getDescription();
            $status = $request->getStatus();
            $date = $request->getDate();
            $deadline = $request->getDeadline();
            $icon = $request->getIcon();
            $price = $request->getRequestedPrice();
            $tangle = $request->getTangleId();
            $tags = $request->getTags();
            $offers = $this->getOfferDetails($requestId);
            $requestDetails = array('requester' => $requester, 'description' => $description,
                'status' => $status, 'date' => $date, 'deadline' => $deadline, 'icon' => $icon,
                'price' => $price, 'tangle' => $tangle, 'tags' => $tags, 'offers' => $offers);
        }
        return $requestDetails;
    }

    /**
     * this returns an array of offers associated with the request
     * @param  Int $requestId  Request id
     * @return Array $offerArray
     * @author sak93
     */
    public function getOfferDetails($requestId) {
        $repository = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer');
        $allOffers = $repository->findAll(array('id' => $requestId));
        $offerArray = array();
        $numOfOffers = count($allOffers);
        for ($i = 0; $i < $numOfOffers; $i++) {
            $offer = $repository->find($allOffers[$i]->getId());
            $request = $offer->getRequestId();
            if ($request == $requestId) {
                $description = $offer->getDescription();
                $status = $offer->getStatus();
                $date = $offer->getDate();
                $deadline = $offer->getExpectedDeadline();
                $price = $offer->getRequestedPrice();
                $details = array('description' => $description, 'status' => $status,
                    'date' => $date, 'deadline' => $deadline, 'price' => $price);
                array_push($offerArray, $details);
            }
        }
        return $offerArray;
    }

}
