<?php

namespace Megasoft\EntangleBundle\Controller;

class MessageController extends Controller
{
    /**
     * 
     * @param integer $request
     * @param integer $offerId
     * @return \Megasoft\EntangleBundle\Controller\Response
     * @author NaderNessem
     */
    public function verify($request,$offerId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        
        if($session == null || $session->getExpired()){
            return new Response('Bad Request', 400);
        }
        if(\is_null($offerId) || \is_null($sessionId)){
            return new Response('Bad Request', 400);
        }
    }
}


