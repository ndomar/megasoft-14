<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Session;
/**
 * Description of ProfileController
 *
 * @author almgohar
 */
class ProfileController {
    
    public function validateUser($userId, $tangleId) {
         $userTangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:UserTangle');
          $userTangle = $userTangleTable->
                findOneBy(array ('userId'=>$userId,'tangleId'=>$tangleId)); 
          if ($userTangle == null) {
              return false;
          } else {
              return true;
          }
    }
    
    public function validateTangle($tangleId) {
        $tangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleTable->findOneBy(array ('tangleId'=>$tangleId));
        if ($tangle == null) {
            return false;
        } else {
            return true;
        }
    }

    public function profile (Request $request, $userId, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if ($sessionId == null) {
            return new Response('Unauthorized',401);
        }
        
        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId'=>$sessionId));
        $loggedInUser = $session->getUserId();
        
        if (!$this->validateTangle($tangleId)) {
            return new Response('Tangle not found',404);
        }
        
        if (!$this->validateUser($loggedInUser, $tangleId)) {
            return new Response('You are not a member of this tangle', 401);
        }
        
        if (!$this->validateUser($userId, $tangleId)) {
            return new Response('The requested user is not'
                    . ' a member of this tangle', 401);
        }
        
        $user = $userTable->findOneBy(array('id'=> $userId));
        
        $offers = $user->getOffers();
        $info = $this->getUserInfo($user, $tangleId);
        $transactions = $this->getTransactions($offers, $tangleId);
        $response = new JsonResponse();
        $response->setData(array('information'=> $info,
            'transactions'=>$transactions));
        $response->setStatusCode(200);
        return $response;       
    }
    
    public function getTransactions ($offers, $tangleId) {
        $transactions = array();
        for ($i = 0; i < count($offers); $i++) {
            $offer = $offers[i];
            if ($offer == null) {
                continue;
            }
            if (($offer->getRequest()->getTangleId() == $tangleId)
                    && ($offer->getTransaction() != null)) {
                $requesterName =$offer->getRequest()->getUser()->getName();
                $requestDescription = $offer->getRequest()->getDescription();
                $amount = $offer->getTransaction()->getRequestedPrice();
                $transactions[] = array('requesterName'=> $requesterName,
                    'requestDescription'=>$requestDescription,
                    'amount'=>$amount);
            }           
        }
        return $transactions;    
    }
    
    public function getUserInfo ($user, $tangleId) {
        if ($user == null) {
            return new Response('Bad Request',400);
        }
        $doctrine = $this->getDoctrine();
        $userId = $user->getId();
        $userTangleTable = $doctrine->
                getRepository ('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleTable->
                findOneBy(array ('userId'=>$userId,'tangleId'=>$tangleId)); 
        $name = $user->getName();
        $description = $user->getUserBio();
        $credit = $userTangle->getCredit();
        $photo = $user->getPhoto();
        $birthdate = $user->getBirthDate();
        $verfied = $user->getVerified();
        $info = array ('name'=>$name , 'description'=>$description , 
            'credit'=>$credit, 'photo'=>$photo , 'birthdate'=>$birthdate , 
            'verified' => $verfied);
        return $info;
    }
    
        
       
}
        
 
