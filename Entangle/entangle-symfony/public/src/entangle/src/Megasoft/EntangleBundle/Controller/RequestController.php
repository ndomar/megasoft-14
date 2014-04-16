<?php

class RequestController extends Controller{
    
    /**
      * Validates that the request has correct format, session Id is active and of a user, request id is of a request 
      * and that the user is the requester of the request
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $requestId
      * @return \Symfony\Component\HttpFoundation\Response, null if no error exists
      */
    private function verifyRequester($request, $requestId){
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
        
        return null;
    }
    
    public function postIconAction(\Symfony\Component\HttpFoundation\Request $request, $requestId){
        $verification = $this->verifyRequester($request, $requestId);
        
        if($verification != null){
            return $verification;
        }
        
        
    }
}