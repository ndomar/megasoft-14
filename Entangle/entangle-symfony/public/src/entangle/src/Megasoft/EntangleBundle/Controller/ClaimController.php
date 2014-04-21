<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Claim extends Controller {
    
    public function validateRequest($request, $tangleId) {
        $flag = true;
        $sessionId = $request->headers->get('X-SESSION-ID');
        if($tangleId == null || $sessionId == null){
            $flag = false;
            return new Response('Bad Request', 400);
        }
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if($session == null || $session->getExpired()){
            $flag = false;
            return new Response('Bad Request', 400);
        }
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if($tangle == null) {
            $flag = false;
            return new Response('Bad Request', 400);
        }
        return $flag;
    }
public function getMails($request, $tangleId)
    {
    $flag = validateRequest($request, $tangleId);
        if($flag == true) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $userId = $session->getUserId();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $offererMail = $userRepo->findOneBy(array('userId' => $userId))-> getEmail();
        $tangleOwnerId = $userTangleRepo->findOneBy(array('tangleId' => $tangleId , 'tangleOwner' => 'true'));
        $tangleOwnerMail = $userRepo->findOneBy(array('userId' => $tangleOwnerId))-> getEmail();
        $response = new JsonResponse();
        $response->setJsonContent
                (array('X-TANGLEOWNER-MAIL' => $tangleOwnerMail, 'X-OFFERER-MAIL' => $offererMail));
        $response->setStatusCode(200);
        return $response;
    }
    }
    
    public function createClaim($request, $tangleId) {
        
        $flag = validateRequest($request, $tangleId);
        if($flag == true) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $subject = $request->body->get('X-SUBJECT');
        $mssgBody = $request->body->get('X-MSSGBODY');
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $userId = $session->getUserId();
        $user = $session->getUser();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId));
        if($mssgBody == null) {
            return new Response('Empty MssgBody', 400);
        }
        sendClaim($user, $userId, $tangle, $tangleId, $mssgBody);
    }
    }
    public function sendClaim($user, $userId, $tangle, $tangleId, $mssgBody) {
        $claim = new Claim();
        $claim->setUser($user);
        $claim->setUserId($userId);
        $claim->setTangle($tangle);
        $claim->setTangleId($tangleId);
        $claim->setMessage($mssgBody);
        $claim->setDeleted('false');
        $response = new JsonResponse();
        $response->setJsonContent(array('X-CLAIM-ID' => $claim->getId()));
        $response->setStatusCode(200);
        return $response; 
    }
}