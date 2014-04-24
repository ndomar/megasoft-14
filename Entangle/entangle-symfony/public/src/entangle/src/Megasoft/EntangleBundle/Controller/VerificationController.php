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
 * @author MahmoudEid
 */

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller {

    /**
     * This method sends verification email to user
     * @param type $userName
     * @param type $userEmail
     * @param type $verificationHash
     * @author MahmoudGamal
     */
    public function emailUserAction($userName, $userEmail, $verificationHash) {
        $body = 'Thanks for signing up!
            
        Your account has been created, you can login with the 
        following user name after you have activated 
        your account by pressing the url below.
 
        ------------------------
        Username: ' . $userName . '
        ------------------------
        Please click this link to activate your account:
        http://www.entangle.com/verify/' . $verificationHash . '';
        $message = \Swift_Message::newInstance()
                ->setSubject('Entangle Verification')
                ->setFrom('mahmoudgamaleid@gmail.com')
                ->setTo($userEmail)
                ->setBody($body);
        $this->get('mailer')->send($message);
        return new Response("Email Sent", 200);
    }

    /**
     * This method changes verified status of user to true
     * @param type $verificationCode
     * @return type
     * @author MahmoudGamal
     */
    public function verifyUserAction($verificationCode) {
        $criteria = array('verificationCode' => $verificationCode);
        $search = $this->getDoctrine()
                ->getRepository('MegasoftEntangleBundle:VerificationCode')
                ->findBy($criteria);
        
        if (!$search) {
            return new Response("User not found" , 404 );
        }
        $user = $search->getUser();
        $user->setVerified(true);
        $this->getDoctrine()->getManager()->flush();
        return new Response("User verified",201);
    }

}
