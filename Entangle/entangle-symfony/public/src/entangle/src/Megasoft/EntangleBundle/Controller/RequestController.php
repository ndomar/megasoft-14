<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RequestController
 *
 * @author Asus
 */
class RequestController extends Controller{
    public function createAction(Request $request , $tangleId)
    {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $json_array = json_decode($json,true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('id'=>$sessionId));
        $userId = $session->getUserId();
        $description = $json_array['description'];
        $tags = $json_array['tags'];
        $date = $json_array['date'];
        $deadLine = $json_array['deadLine'];
        $requestedPrice = $json_array['requestedPrice'];
        
        $Request = new Request();
        $Request->setDescription($description);
        $Request->setStatus('pending');
        $Request->setTangleId($tangleId);
        $Request->setDate($date);
        $Request->setDeadLine($deadLine);
        $Request->setUserId($userId);
        $Request->setRequestedPrice($requestedPrice);
        
        addTags($request , $tags);
      
        $doctrine->getManager()->persist($Request);
        $doctrine->getManager()->flush();
        
        $response = new JsonResponse();
        $response->setData(array('sessionId'=>$sessionId));
        $response->setStatusCode(201);
        return $response;
    }
    
    
    public function addTags($Request , $tags){
        $tagElements = explode("," , $tags);
         $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Tag');
        
        for($i =0;i<$tagElements->length;$i++){
        $tag = $repo->findOneBy(array('name'=>$tagElements[i]));
        if($tag==null){
            $tag = new Tag();
            $tag->setName($tagElements[$i]);
        }
            $Request->addTag($tag);
            
        }
    }
}
