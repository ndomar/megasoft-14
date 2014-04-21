<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MailController extends Controller
{


    /**
     * @return Response
     */
    public function SendEmailAction()
    {
        $this->sendMail(1, "el 3amleya nesr", "kolo shenkan ya regala");
        return new Response("OK", 200);
    }


    /**
     * this is the main function that sends emails
     * @param $userID
     * @param $subject
     * @param $body
     */
    public function sendMail($userID, $subject, $body)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');

        $user = $repo->find($userID);
        $useremail = $doctrine->getRepository("MegasoftEntangleBundle:UserEmail")->findBy(array('userId' => $user->getId(), 'deleted' => 0));


        foreach ($useremail as $mail) {
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('Notifications-noreply@entangle.io')
                ->setTo($mail->getEmail())
                ->setBody($body);
            $this->get('mailer')->send($message);
        }
    }
}
