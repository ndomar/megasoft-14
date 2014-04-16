<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RequestController extends Controller{
    
    /**
      * A function to save an icon and return the url to it
      * @param string $iconData
      * @param integer $requestId
      * @return string $url
      * @author OmarElAzazy
      */
    private function saveIcon($iconData, $requestId){
        $iconFileName = 'request#' . "$requestId" . '.png';
        $outputFilePath = '/vagrant/public/src/entangle/web/bundles/megasoftentangle/images/icons/' . $iconFileName;
        file_put_contents($outputFilePath, $iconData);
        return 'http://10.11.12.13/entangle/web/bundles/megasoftentangle/images/icons/' . $iconFileName;
    }
    
    /**
      * An endpoint to set the icon of a request
      * @param Request $request
      * @param integer $requestId
      * @return Response */
    public function postIconAction(Request $request, $requestId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if($requestId == null || $sessionId == null){
            return new Response('Bad Request', 400);
        }
        
        $doctrine = $this->getDoctrine();
        
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if($session == null || $session->getExpired()){
            return new Response('Bad Request', 400);
        }
        
        $jsonString = $request->getContent();
        
        if($jsonString == null){
            return new Response('Bad Request', 400);
        }
        
        $json = json_decode($jsonString, true);
        $iconData = $json['requestIcon'];
        
        if($iconData == null){
            return new Response('Bad Request', 400);
        }
        
        $requesterId = $session->getUserId();
        
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        if($request == null || $request->getUserId() != $requesterId){
            return new Response('Unauthorized', 401);
        }
        
        try{
            $iconUrl = $this->saveIcon($iconData, $requestId);
        }
        catch (Exception $e){
            return new Response('Internal Server Error', 500);
        }
        
        $request->setIcon($iconUrl);
        
        $this->getDoctrine()->getManager()->persist($request);
        $this->getDoctrine()->getManager()->flush();
        
        $response = new JsonResponse();
        $response->setData(array('iconUrl' => $iconUrl));
        return $response;
    }
}