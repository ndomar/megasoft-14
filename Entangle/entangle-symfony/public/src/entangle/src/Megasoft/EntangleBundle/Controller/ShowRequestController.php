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
        $allOffers = $repository->findAll(array('id'=>$requestId));
        $returnedOfferArray = array();
        $numOfOffers=count($allOffers);
        if($numOfOffers === 0){
            array_push($returnedOfferArray,"No offers yet");
            return $returnedOfferArray; 
        }
        for($i=0; $i<$numOfOffers;$i++){
            $offerId = $allOffers[$i]->getId();
            $offer = $repository->find($offerId);
            $description = $offer->getDescription();
            $status = $offer->getStatus();
            $date = $offer->getDate();
            $deadline = $offer->getExpectedDeadline(); 
            $price = $offer->getRequestedPrice(); 
            $details = array('description'=>$description , 'status'=>$status,
            'date'=>$date, 'deadline'=>$deadline,'price'=>$price);
            array_push($returnedOfferArray, $details); 
        }
        return $returnedOfferArray; 
    }
    
}
