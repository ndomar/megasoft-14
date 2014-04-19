<?php

namespace Megasoft\EntangleBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tag;
/**
 * Description of RequestController
 *
 * @author Salma Khaled
 */
class RequestController extends Controller{
    public function createAction(\Symfony\Component\HttpFoundation\Request $request , $tangleId)
    {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $response = new JsonResponse();
        $json_array = json_decode($json,true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId'=>$sessionId));
       // return new Response($sessionId);
       /* if($session == null){
        $response->setStatusCode(400);
        return $response;
        }*/
       // $userId = $session->getUserId();
        $description = $json_array['description'];
        $tags = $json_array['tags'];
        $date = $json_array['date'];
        $deadLine = $json_array['deadLine'];
        $requestedPrice = $json_array['requestedPrice'];
        
        $newRequest = new Request();
        $newRequest->setDescription($description);
        $newRequest->setStatus(1);
        $newRequest->setTangleId($tangleId);
        $newRequest->setDate($date);
        $newRequest->setDeadLine($deadLine);
       // $newRequest->setUserId($userId);
        $newRequest->setRequestedPrice($requestedPrice);
        
        $this->addTags($newRequest , $tags);
      
        $doctrine->getManager()->persist($newRequest);
        $doctrine->getManager()->flush();
        
        
        $response->setData(array('sessionId'=>$sessionId));
        $response->setStatusCode(201);
        return $response;
    }
    
    
    public function addTags($newRequest , $tags){
       // $tagElements = explode("," , $tags);
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Tag');
        $arrlength = count($tags);
        for($i =0;$i<$arrlength;$i++){
        $tag = $repo->findOneBy(array('name'=>$tags[$i]));
        if($tag==null){
            $tag = new Tag();
            $tag->setName($tags[$i]);
        }
            $newRequest->addTag($tag);
            $doctrine->getManager()->persist($tag);
            $doctrine->getManager()->flush();
            
        }
    }
}
