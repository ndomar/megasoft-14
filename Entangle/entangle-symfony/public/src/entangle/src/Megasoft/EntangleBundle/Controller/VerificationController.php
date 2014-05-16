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
     * This method Verifies additional emails for user
     * @param String $verificationCode
     * @return twig view
     * @author MahmoudGamal
     */
    public function verifyEmailAction($verificationCode) {
        $criteria = array('verificationCode' => $verificationCode);
        $search = $this->getDoctrine()
                ->getRepository('MegasoftEntangleBundle:VerificationCode')
                ->findOneBy($criteria);
        if (!$search) {
           return $this->render('MegasoftEntangleBundle:Verified:notfound.html.twig');
        }
        $UserEmail = $search->getUserEmail();
        $expired = $search->getExpired();
        if ($expired) {
            return $this->render('MegasoftEntangleBundle:Verified:expired.html.twig');
        }
        $username = $UserEmail->getUser()->getName();
        $UserEmail->setVerified(true);
        $search->setExpired(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('MegasoftEntangleBundle:Verified:verified.html.twig',array(
        'username' => $username,));
    }

}
