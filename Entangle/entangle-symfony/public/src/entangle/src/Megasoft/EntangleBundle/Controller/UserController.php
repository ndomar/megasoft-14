<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
/**
 * Description of ProfileController
 *
 * @author almgohar
 */
class ProfileController {
    
    //gets all required info to view a user profile
    //returns a jSonResponse
    public function profile($userId, $tangleId, $sessionId) {
        $doctrine = $this->getDoctrine();
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('id'=>$sessionId));
        $user = $userTable->findOneBy(array('id'=> $userId));
        $offers = $user->getOffers();
        $info = $this->getUserInfo($user);
        $transactions = $this->getTransactions($offers, $tangleId);
        $loggedInUser = $session->getUserId(); //Id of the currently logged in user
        
        if($userId == $loggedInUser) {
            $login = true;
        } else {
            $login = false;
        }
        
            $response = new JsonResponse();
            $response->setData(array('loggedIn'=>$login, 'information'=> $info, 
                'transactions'=>$transactions));
            $response->setStatusCode(200);
            return $response;
        }
   
    //gets the transactions of this user in a certain tangle
    //returns and array of arrays
    public function getTransactions($offers, $tangleId) {
        $transactions = array();
        for($i = 0; i < count($offers); $i++) {
            $offer = $offers[i];
            if($offer == null) {
                continue;
            }
            if(($offer->getRequest()->getTangleId() == $tangleId) 
                    && ($offer->getTransaction() != null)) { 
                $requesterName =$offer->getRequest()->getUser()->getName();
                $requestDescription = $offer->getRequest()->getDescription();
                $amount = $offer->getTransaction()->getRequestedPrice();
                $transactions[] = array('requesterName'=> $requesterName, 
                    'requestDescription'=>$requestDescription,'amount'=>$amount);
            }
            }
            return $transactions;
    }
    
    //gets the basic information of the required user
    //returns an array of these information
    public function getUserInfo($user){
        if($user == null){
            return new Response('Bad Request',400);
        }
            $name = $user->getName();
            $description = $user->getUserBio();
            $photo = $user->getPhoto();
            $birthdate = $user->getBirthdate();
            $verfied = $user->getVerified();
            $info = array('name'=>$name, 'description'=>$description,
                'photo'=>$photo, 'birthdate'=>$birthdate, 'verified' => $verfied);
            return $info;
    }
    
        
       
}
        
 
