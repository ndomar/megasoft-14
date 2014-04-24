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


namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Offer;

/**
 * Gets the required information to view a certain offer
 * @author Almgohar
 */
class OfferController extends Controller {
    /**
     * 
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @param integer $sessionId
     * @return boolean true if the user can view this request and false otherwise
     * @author Almgohar
     */
    private function validateUser($request,$sessionId) {
        $sessionTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:Session');
        $userTangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:UserTangle');
        $session = $sessionTable->findOneBy(array('sessionId'=>$sessionId));
        $loggedInUser = $session->getUserId();
        $tangleId = $request->getTangleId();
             
        $userTangle = $userTangleTable->
                findOneBy(array ('userId'=>$loggedInUser,'tangleId'=>$tangleId)); 

        if ($userTangle == null) {
            return false;
        } else {
            return true;
        }   
    }
    
    /**
     * Sends the required information in a JSon response
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param integer $offerId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author Almgohar
     */
    public function offerAction
            (\Symfony\Component\HttpFoundation\Request $req, $offerId) {
   
      $sessionId = $req->headers->get('X-SESSION-ID'); 
      
      if ($sessionId == null) {
          return new Response('Unauthorized',401);
      }
      
      $doctrine = $this->getDoctrine();
       $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
      $session = $sessionTable->findOneBy(array('sessionId'=>$sessionId));
     
      if($session == null || $session->getExpired()) {
          return new Response('Unauthorized',401);
      }
      
      $offerTable = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
      $offer = $offerTable->findOneBy(array('id'=>$offerId));
     
      if($offer == null || $offer->getDeleted()) {
          return new Response('Offer not found',404);
      }
      
     
      $request = $offer->getRequest();
      
      if($request->getDeleted()) {
          return new Response("Request not found",404);
      }
      
      $tangleId = $request->getTangleId();
     
      
      if(!$this->validateUser($request, $sessionId)) {
          return new Response('Unauthorized',401); 
      }
      
      $requestInformation = $this->getRequestInformation($request);
      $offerInformation = $this->getOfferInformation($offer);
      $response = new JsonResponse(null, 200);
      $response->setData(array('tangleId'=> $tangleId,
          'requestInformation'=>$requestInformation, 
          'offerInformation'=>$offerInformation));
      return $response;
    }

    /**
     * Gets the request information
     * @param \Megasoft\EntangleBundle\Entity\Request $request $request
     * @return array $requestInformation
     * @author Almgohar
     */
    private function getRequestInformation($request) {
        $user = $request->getUser();
        
        $userId = $user->getId();
        $userName = $user->getName();
        
        $requestId = $request->getId();
        $requestStatus = $request->getStatus();
        $requestDescription = $request->getDescription();
        
        $requestInformation [] = array('requesterName' => $userName, 
            'requestDescription'=> $requestDescription, 'requesterID'=> $userId,
            'requestID'=>$requestId,'requestStatus'=>$requestStatus);
        
        return $requestInformation;  
    }

    /**
     * 
     * @param \Megasoft\EntangleBundle\Entity\Offer $offer
     * @return array $offerInformation
     * @author Almgohar
     */
    private function getOfferInformation($offer) {
        $user = $offer->getUser();
        
        $userId = $user->getId();
        $userName = $user->getName();
        $offerDate = $offer->getDate();
        $offerStatus = $offer->getStatus();
        $offerPrice = $offer->getRequestedPrice();
        $offerDescription = $offer->getDescription();
        $offerDeadline = $offer->getExpectedDeadline();
    
        $offerInformation [] = array('offererName'=> $userName,
            'offerDescription'=> $offerDescription,
            'offerDeadline'=> $offerDeadline,
            'offerStatus'=> $offerStatus,
            'offerPrice'=> $offerPrice,
            'offerDate'=> $offerDate,
            'offererID'=> $userId);
    
        return $offerInformation;
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

