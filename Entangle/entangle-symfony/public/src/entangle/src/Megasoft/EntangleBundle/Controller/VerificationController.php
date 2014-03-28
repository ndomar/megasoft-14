<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VerificationController
 *this controller controls the process of sending emails to users
 * and verifying them 
 * @author MahmoudEid
 */
namespace Megasoft\EntangleBundle\Controller;

class VerificationController {
    
/**
 * Description of emailUserAction
 *this method takes user name , user email and the verification hash and sends
 * and e-mail to the user containing their verification link
 * @author MahmoudEid
 */
    public function emailUserAction($userName , $userEmail , $verificationHash)
    {
        $body = 'Thanks for signing up!
            
        Your account has been created, you can login with the 
        following user name after you have activated 
        your account by pressing the url below.
 
        ------------------------
        Username: '.$userName .'
        ------------------------
        Please click this link to activate your account:
        http://www.entangle.com/verify/'.$verificationHash.''; 
        $message = \Swift_Message::newInstance()
        ->setSubject('Entangle Verification')
        ->setFrom('mahmoudgamaleid@gmail.com')
        ->setTo($userEmail)
        ->setBody($body);
        $this->get('mailer')->send($message);  
        
    }
    /**
 * Description of VerifyUserAction
 *this method takes the Verification hash , and looks for the member 
 * which that hash belongs to and changes his status to verified
 * @author MahmoudEid
 */
    public function verifyUserAction ($verificationHash){
        $search = $this->getDoctrine()
                 ->getRepository('EntangleBundle:Entity:verification')
                 ->findBy(array('hashCode'=>$verificationHash));
        $user =  $search->getUser();
        $user->setVerified(true);
  
          return $this->render('MegasoftEntangleBundle:Default:index.html.twig', array('name' => $user->$verificationHash));
    }
}
