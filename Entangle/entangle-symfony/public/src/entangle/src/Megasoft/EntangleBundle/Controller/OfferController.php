<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Offer;


class OfferController extends Controller
{
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

    public function viewAction(Request $req, $offerId) {
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
    
    if(!$this->validateUser($request, $sessionId)) {
        return new Response('Unauthorized',401);     
    }
    $requestInformation = $this->getRequestInformation($request);
    $offerInformation = $this->getOfferInformation($offer);
    $response = new JsonResponse(null, 200);
    $response->setData(array('requestInformation'=>$requestInformation, 
        'offerInformation'=>$offerInformation));
    return $response;    
}

public function getRequestInformation($request) {
    $user = $request->getUser();
    $userName = $user->getName();
    $requestDescription = $request->getDescription();
   
    $requestInformation [] = array('requesterName' => $userName, 
        'requestDescription'=> $requestDescription);
    
    return $requestInformation;
}

public function getOfferInformation($offer) {
    $user = $offer->getUser();
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
        'offerDate'=> $offerDate);
    
    return $offerInformation;
            
   
    
}
   

}
