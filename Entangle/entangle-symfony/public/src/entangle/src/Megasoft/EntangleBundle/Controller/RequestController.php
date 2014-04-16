<?php

class RequestController extends Controller{
    
    /**
      * A function to save an icon and return the url to it
      * @param string $iconData
      * @param integer $requestId
      * @return string $url
      * @author OmarElAzazy
      */
    private function saveIcon($iconData, $requestId){
        return 'http://10.11.12.13/entangle/web/bundles/megasoftentangle/images/icons/test.jpg';
    }
    
    /**
      * An endpoint to set the icon of a request
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $requestId
      * @return \Symfony\Component\HttpFoundation\Response
      * @author OmarElAzazy
      */
    public function postIconAction(\Symfony\Component\HttpFoundation\Request $request, $requestId){
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
        
        $requesterId = $session->getUserId();
        
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('requestId' => $requestId));
        if($request == null || $request->getUserId() != $requesterId){
            return new Response('Unauthorized', 401);
        }
        
        $json = $request->getContent();
        
        if($json == null){
            return new Response('Bad Request', 400);
        }
        
        $json_array = json_decond($json, true);
        $iconData = $json_array['requestIcon'];
    }
}