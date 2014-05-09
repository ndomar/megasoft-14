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
    public function sendUserVerificationEmailAction($userName, $userEmail, $verificationHash) {
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
     * This method sends Email verification mail to user
     * @param String $userName
     * @param String $userEmail
     * @param String $verificationHash
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendEmailVerificationEmailAction($userName , $userEmail , $verificationHash){
        $body = 'Hello' .$userName .'
            
                You have added this email as an additional email to your account.
                If you did so please click on the following link to verify:
                
                http://www.entangle.com/verify/email/' . $verificationHash . '
                If this is not you please ignore this email';
        $subject = 'Entangle Additional Email verification';
        sendMailToEmail($userEmail , $subject , $body);
        return new Response("Email Sent",200);
    }
    /**
     * This method changes the verified parameter of the user to true
     * @param String $verificationCode
     * @return twig view
     * @author MahmoudGamal
     */
    public function verifyUserAction($verificationCode) {
        $criteria = array('verificationCode' => $verificationCode);
        $search = $this->getDoctrine()
                        ->getRepository('MegasoftEntangleBundle:VerificationCode')
                        ->findOneBy($criteria);
        if (!$search) {
             return $this->render('MegasoftEntangleBundle:Verified:notfound.html.twig');
        }
        $expired = $search->getExpired();
        if ($expired) {
            return $this->render('MegasoftEntangleBundle:Verified:expired.html.twig');
        }
        $user = $search->getUser();
        $username = $user->getName();
        $user->setVerified(true);
        $search->setExpired(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('MegasoftEntangleBundle:Verified:verified.html.twig',array(
        'username' => $username));
    }
    /**
     * This method Verifies additional emails for user
     * @param String $verificationCode
     * @return twig view
     */
    
    public function verifyEmailAction($verificationCode) {
        $criteria = array('verificationCode' => $verificationCode);
        $search = $this->getDoctrine()
                ->getRepository('MegasoftEntangleBundle:VerificationCode')
                ->findOneBy($criteria);
        $UserEmail = $search->getEmail();
        $expired = $search->getExpired();
        $username = $search->getUser()->getName();
        if (!$search) {
           return $this->render('MegasoftEntangleBundle:Verified:notfound.html.twig');
        }
        if ($expired) {
            return $this->render('MegasoftEntangleBundle:Verified:expired.html.twig');
        }
        $UserEmail->setVerified(true);
        $search->setExpired(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('MegasoftEntangleBundle:Verified:emailVerified.html.twig',array(
        'username' => $username));
    }
}
