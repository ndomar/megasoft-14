<?php

class RequestController extends Controller{
    
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
        
    }
}