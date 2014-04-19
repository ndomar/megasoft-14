<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller {

    /**
     * this function sends an email notification to user with userID
     * @param  Int      $userid    user ID
     * @param  String   $subject   The subject of the email notification
     * @return string   $body      The body of the email notification
     * @author amrelzanaty

     */
    public function SendEmailAction($userID, $subject, $body) {

        $doctrine = $this->getDoctrine();
       $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');

       $user = $repo->findOneBy(array('id' => $userID));
       $useremail = $user->getEmail();


        $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('Notifications-noreply@entangle.io')
                ->setTo($useremail)
                ->setBody($body)

        ;
        $this->get('mailer')->send($message);

        return new Response("OK", 200);
    }

}
