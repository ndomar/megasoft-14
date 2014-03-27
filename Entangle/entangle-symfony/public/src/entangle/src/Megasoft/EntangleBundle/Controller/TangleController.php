<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Tests\Controller;

class TangleController extends Controller
{   
    
    private function requestsToJsonArray($requests){
        
        $requestsJsonArray = array();
        foreach($requests as $request){
            $requestJson = array(
                'id' => $request->getId(),
                'username' => $request->getUser()->getName(),
                'userId' => $request->getUserId(),
                'description' => $request->getDescription(),
                'offersCount' => \sizeof($request->getOffers())
            );
            array_push($requestsJsonArray, $requestJson);
        }
        
        $jsonArray = array('count' => sizeof($requests), 'requests' => $requestsJsonArray);
        
        return $jsonArray;
    }
    
    public function allRequestsAction($tangleId, Request $request)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if(\is_null($tangleId) || \is_null($sessionId)){
            return new Response('Bad Request', 400);
        }
        
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('EntangleBundle:Tangle');
        
        $tangle = $repo->findOneBy(array('tangleId' => $tangleId));
        
        if(\is_null($tangle)){
            return new Response('Not Found', 404);
        }
        
        $requests = $tangle->getRequests();
        
        $response = new JsonResponse();
        $response->setData($this->requestsToJsonArray($requests));
        
        return $response;
    }
}
