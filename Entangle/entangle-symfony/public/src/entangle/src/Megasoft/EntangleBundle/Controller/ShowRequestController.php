<?php

namespace Megasoft\EntangleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowRequestController extends Controller
{
    public function getRequestAttibutesAction($requestId){
        $doctrine = $this->getDoctrine();
        $repository = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $repository->findOneBy(array('id'=>$requestId));
        if(count($request)==0){
            return new Response("No such request" , 404 );
        }
        else{
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
        $response = new JsonResponse();
        $response->setData(array($arr));
        $response->setStatusCode(200);
        return $response;
        }
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
