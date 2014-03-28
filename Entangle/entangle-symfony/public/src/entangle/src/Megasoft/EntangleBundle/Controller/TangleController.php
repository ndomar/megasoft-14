<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Tests\Controller;

class TangleController extends Controller
{
    
    public function filterRequestsAction(Request $request, $tangleId)
    { 
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if($tangleId == null || $sessionId == null){
            return new Response('Bad Request', 400);
        }
        
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('EntangleBundle:Session');
        $session = $sessionRepo->getOneBy(array('sessionId' => $sessionId));
        if($session == null){
            return new Response('Bad Request', 400);
        }
        
        $user = $session->getUser();
        $userTangleRepo = $doctrine->getRepository('EntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->getOneBy(array('tangleId' => $tangleId, 'userId' => $user->getId()));
        
        if($userTangle == null){
            return new Response('Unauthorized', 401);
        }
        
        $criteria = array('tangleId' => $tangleId);
        
        $userId = $request->query->get('userid', null);
        if($userId != null){
            $criteria['userId'] = $userId;
        }
        
        $fullText = $request->query->get('fulltext', null);
        if($fullText != null){
            $criteria['description'] = $fullText;
        }
        
        $requestRepo = $doctrine->getRepository('EntangleBundle:Request');
        $requests = $requestRepo->getBy($criteria);
        
        $tagId = $request->query->get('tagid', null);
        $usernamePrefix = $request->query->get('usernameprefix', null);
        $requestsJsonArray = array();
        
        foreach($requests as $tangleRequest){
            
            if($tagId != null){
                $foundTag = false;
                foreach($tangleRequest->getTags() as $tag){
                    if($tag->getId() == $tagId){
                        $foundTag = true;
                        break;
                    }
                }
                
                if(!$foundTag){
                    continue;
                }
            }
            
            if($usernamePrefix != null){
                $user = $tangleRequest->getUser();
                if(!startsWith($user->getName(), $usernamePrefix)){
                    continue;
                }
            }
            
            $requestsJsonArray[] = array(
                                        'id' => $request->getId(),
                                        'username' => $request->getUser()->getName(),
                                        'userId' => $request->getUserId(),
                                        'description' => $request->getDescription(),
                                        'offersCount' => \sizeof($request->getOffers())
                                    );
        }
        
        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($requestsJsonArray), 'requests' => $requestsJsonArray));
        
        return $response;
    }
}
