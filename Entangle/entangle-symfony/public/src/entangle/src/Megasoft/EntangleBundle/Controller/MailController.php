<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller {

    /**
     * @return Response
     * @author amrelzanaty
     */
    public function SendEmailAction() {
        $this->sendMail(1, "Hello world ", "Welcome to Entangle");
        return new Response("OK", 200);
    }

    /**
     * this function sends an email notification to user with userID
     * @param  Int       $userid    user ID
     * @param  String    $subject   The subject of the email notification
     * @param  boolean   $maildeleted decides if the mail is deleted or not
     * @param  boolean   $accepmail decides if the the user accepts mail or not
     * @return string    $body  The body of the email notification
     * @author amrelzanaty

     */
    public function sendMail($userID, $subject, $body) {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->find($userID); 
      
        $acceptmail =$repo->find(array('acceptMailNotifications' => $user->getAcceptMailNotifications())); 
        
        $mailrepo =  $doctrine->getRepository("MegasoftEntangleBundle:UserEmail");
        
        $useremail = $mailrepo->findBy(array('userId' => $user->getId(), 'deleted' => 0));
        
        $maildeleted = $mailrepo->findBy(array('deleted' => $useremail->getDeleted())); 
        

        if ($acceptmail == true ) {
            foreach ($useremail as $mail) {
                if ($maildeleted == false ) {
                    $message = \Swift_Message::newInstance()
                            ->setSubject($subject)
                            ->addFrom('Notifications-noreply@entangle.io', 'Entangle')
                            ->setTo($mail->getEmail())
                            ->setBody($body);
                     $this->get('mailer')->send($message);
                }
            }
        }
    }
}
     