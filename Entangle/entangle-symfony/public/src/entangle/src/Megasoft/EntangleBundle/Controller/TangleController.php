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
    
    private function getUserBySessionId($sessionId){
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('EntangleBundle:Session');
        
        $session = $repo->findOneBy(array('sessionId' => $sessionId));
        
        return $session->getUser();
    }
    
    private function userInTangle($sessionId, $tangleId){
        $user = $this->getUserBySessionId($sessionId);
        $userTangles = $user->getUserTangles();
        
        $tangleFound = false;
        foreach($userTangles as $userTangle){
            if($userTangle->getTangleId() == $tangleId){
                $tangleFound = true;
                break;
            }
        }
        
        return $tangleFound;
    }
    
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
        $userId = $request->query->get('userid', null);
        $tagId = $request->query->get('tagid', null);
        $fullText = $request->query->get('fulltext', null);
        
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if(\is_null($tangleId) || \is_null($sessionId)){
            return new Response('Bad Request', 400);
        }
        
        if(!$this->userInTangle($sessionId, $tangleId)){
            return new Response('Unauthorized', 401);
        }
        
        $criteria = array('tangleId' => $tangleId);
        if(!\is_null($userId)){
            $critera['userId'] = $userId;
        }
        if(!\is_null($tagId)){
            $critera['tagId'] = $tagId;
        }
        if(!\is_null($fullText)){
            $criteria['description'] = $fullText;
        }
        
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('EntangleBundle:Tangle');
        $tangle = $repo->findOneBy($criteria);
        
        if(\is_null($tangle)){
            return new Response('Not Found', 404);
        }
        
        $requests = $tangle->getRequests();
        
        $response = new JsonResponse();
        $response->setData($this->requestsToJsonArray($requests));
        
        return $response;
    }
}
