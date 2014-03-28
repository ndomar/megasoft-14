<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ShowRequestContoller extends Controller
{
    public function getRequestAttibutes($requestId){
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $repository->findOneBy(array('id'=>$requestId));
        $requester = $request->getUserId(); 
        $description = $request->getDescription();
        $status = $request->getStatus();
        $date = $request->getDate(); 
        $deadline = $request->getDeadline(); 
        $icon = $request->getIcon();
        $price = $request->getRequestedPrice(); 
        $tangle = $request->getTangleId(); 
        $tags = $request->getTags();
        $arr = array('requester'=>$requester, 'description'=>$description , 
            'status'=>$status,'date'=>$date, 'deadline'=>$deadline,
            'icon'=>$icon, 'price'=>$price, 'tangle'=>$tangle, 'tags'=>$tags);
        return $arr; 
    }
    
    public function getRequestDetails(Request $request)
    {
        $json = $request->getContent();
        $json_array = json_decode($json,true);
        if(count($json_array)==0){
            return new Response("Request Deleted" , 404 );
        }
        $requestId = $json_array['id'];
        $arr = $this->getRequestAttibutes($requestId);
        $response = new JsonResponse();
        $response->setData($arr);
        $response->setStatusCode(201);
        return $response;
    }
    public function getOfferAttributes($offerId){
       $doctrine = $this->getDoctrine(); 
       $repository = $doctrine->getRepository('MegasoftEntangleBundle: Offer'); 
       $offer = $repository->find($offerId);
       $date = $offer->getDate(); 
       $deadline = $offer->getExpectedDeadline(); 
       $description = $offer->getDescription();
       $status = $offer->getStatus();
       $price = $offer->getRequestedPrice(); 
       $arr = array('description'=>$description , 'status'=>$status,
           'date'=>$date, 'deadline'=>$deadline,'price'=>$price);
        return $arr; 
       
    }
    public function getOffers($requestID){
        $doctrine = $this->getDoctrine(); 
        $repository = $doctrine->getRepository('MegasoftEntangleBundle: Offer');
        $offers = $repository->findAll($requestID);
        $numOfOffers=count($offers);
        $response = new JsonResponse(); 
        for($i=0; $i<$numOfOffers;$i++){
         $offer = $offers[i];
         $offerId = $offer->getId();
         $arr = $this->getOfferAttributes($offerId);
         $jsonOffer= new JsonResponse();
         $jsonOffer->setData($arr);
         $response->setData($jsonOffer);
         }
      
        return $response; 
        
    }
}