<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Offer;

/**
 * Gets the required information to view a certain offer
 * @author Almgohar
 */
class OfferController extends Controller
{
    /**
     * 
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @param integer $sessionId
     * @return boolean true if the user can view this request and false otherwise
     * @author Almgohar
     */
    public function validateUser($request,$sessionId) {
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
    public function getOfferAction(Request $req, $offerId) {
      if (offerId == null) {
          return new Response('Offer not found', 404);
      }
      $sessionId = $req->headers->get('X-SESSION-ID'); 
      
      if ($sessionId == null) {
          return new Response('Unauthorized',401);
      }
      
      $doctrine = $this->getDoctrine();
      $offerTable = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
      $offer = $offerTable->findOneBy(array('id'=>$offerId));
      $request = $offer->getRequest();
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
    public function getRequestInformation($request) {
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
    public function getOfferInformation($offer) {
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
