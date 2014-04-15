<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MailController extends Controller {

    public function registerBundles() {
        $bundles = array(
            // ...

            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
        );
    }

    public function SendMailNotifications($usermail) {
        $message = \Swift_Message::newInstance()
                ->setSubject('Hello Email')
                ->setFrom('Entanglezanaty@gmail.com')
                ->setTo('amrelzanati@gmail.com')
                ->setBody(
                $this->renderView(
                        'HelloBundle:Hello:email.txt.twig', array('name' => $user)
                )
                )
        ;
        $this->get('mailer')->send($message);

        return $this->render();
    }

    function testAction() {
        $email = "mohamed19936@gmail.com";
        $message = "hello shaban";
        $subject = "test test test";
        sendMails($email, $subject, $message);
    }

}
