<?php

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
   

}
