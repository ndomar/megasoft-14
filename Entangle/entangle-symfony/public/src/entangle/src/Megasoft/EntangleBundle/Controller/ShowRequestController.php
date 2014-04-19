<?php

namespace Megasoft\EntangleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $offers = $this->getOffers($requestId);
        $arr = array('requester'=>$requester, 'description'=>$description , 
            'status'=>$status,'date'=>$date, 'deadline'=>$deadline,
            'icon'=>$icon, 'price'=>$price, 'tangle'=>$tangle, 'tags'=>$tags, 'offers'=>$offers);
        $response = new JsonResponse();
        $response->setData(array($arr));
        $response->setStatusCode(200);
        return $response;
        }
    }
    
    public function getOffers($requestId){
        $doctrine = $this->getDoctrine(); 
        $repository = $doctrine->getRepository('MegasoftEntangleBundle:Offer'); 
        $offers = $repository->findAll(array('id'=>$requestId));
      //  $offerDetails = array("date","deadline", "description", "status", "price");
        $arr2 = array();
        $numOfOffers=count($offers);
        if($numOfOffers === 0){
            array_push($arr2,"No offers yet");
            return $arr2; 
        }
        $response = new JsonResponse();
        for($i=0; $i<$numOfOffers;$i++){
            $offerId = $offers[$i]->getId();
            $offer = $repository->find($offerId);
            $date = $offer->getDate();
            $deadline = $offer->getExpectedDeadline(); 
            $description = $offer->getDescription();
            $status = $offer->getStatus(); 
            $price = $offer->getRequestedPrice(); 
          
            $arr = array('description'=>$description , 'status'=>$status,
            'date'=>$date, 'deadline'=>$deadline,'price'=>$price);
            array_push($arr2, $arr);
        
        }
        $response->setData($arr2); 
       
        return $arr2; 
    }
    
}
