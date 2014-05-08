<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VerificationController
 * this controller controls the process of sending emails to users
 * and verifying them 
 * @author MahmoudGamal
 */

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller {

    /**
     * This method sends verification email to user
     * @param String $userName
     * @param String $userEmail
     * @param String $verificationHash
     * @return  response
     * @author MahmoudGamal
     */
    public function emailUserAction($userName, $userEmail, $verificationHash) {
        $body = 'Welcome to Entangle!
            
        Your account has been created, you can 
        verify your account by clicking the link below
 
        ------------------------
        Username: ' . $userName . '
        ------------------------
        Please click this link to verify your account:
        http://www.entangle.com/verify/' . $verificationHash . '';
        $subject = 'Entangle user verification';
        sendMailToEmail($userEmail, $subject, $body);
        return new Response("Email Sent", 200);
    }

    /**
     * This method changes the verified parameter of the user to true
     * @param String $verificationCode
     * @return \Symfony\Component\HttpFoundation\Response
     * @author MahmoudGamal
     */
    public function verifyUserAction($verificationCode) {
        $criteria = array('verificationCode' => $verificationCode);
        $search = current($this->getDoctrine()
                        ->getRepository('MegasoftEntangleBundle:VerificationCode')
                        ->findBy($criteria));
        $user = $search->getUser();
        $expired = $search->getExpired();
        if ($expired) {
            return new Response("Verification Link expired", 400);
        }
        $verified = $user->getVerified();
        if ($verified) {
            return new Response("User Already Verified", 400);
        }
        if (!$search) {
            return new Response("User not found", 404);
        }
        $user->setVerified(true);
        $search->setExpired(true);
        $this->getDoctrine()->getManager()->flush();
        return new Response("User verified", 201);
    }
    
    
}
