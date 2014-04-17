<?php

namespace Megasoft\EntangleBundle\Controller;

class MessageController extends Controller
{
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
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
    /**
     * getting all messages on that offer
     * @param \Megasoft\EntangleBundle\Controller\Request $request
     * @param  integer $offerId
     * @return \Megasoft\EntangleBundle\Controller\Response $response
     * @author NaderNessem
     */
    public function getMessages(Request $request,$offerId){
        $verify = verify($request,$offerId);
        if(\is_null($verify)){
            return verify;
        }
        $doctrine = $this->getDoctrine();
        $messagesRepo = $doctrine->getRepository('MegasoftEntangleBundle:Message');
        $messages = $messagesRepo->findOneBy(array('offerId' => $offerId));
        $jsonArray = array();
        foreach($messages as $message) {
            $senderId= $message->getSenderId();
            $senderRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
            $sender = $senderRepo->findOneBy(array('id' => $senderId));
            $jsonArray ['senderName'] =$sender->getName();
            $jsonArray ['body'] = $message->getBody();
            $jsonArray ['date'] = $message->getDate();
        }
        $response = json_encode($jsonArray);
        return $response;
        
    }
    
    
}


