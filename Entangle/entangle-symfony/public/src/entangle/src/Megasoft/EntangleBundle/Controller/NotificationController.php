<?php

/**
 * @author Mohamed Shaban
 *
 */


namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Class NotificationController
 * @package Megasoft\EntangleBundle\Controller
 */
class NotificationController extends Controller
{

    /**
     * this action is here just for testing purposes
     * this is a test action just to test notification center function
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function testAction()
    {
        $nc = $this->get('notification_center.service');
        $response = new JsonResponse();
        $title = "new message";
        $body = "{{from}} made new message";
        $name = $nc->newMessageNotification(0, $title, $body);
        $response->setData($name);
        return $response;
    }

    /**
     * this searches for session ID and adds a new regId to this session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     */
    function registerAction(Request $request)
    {
        $sessionid = $request->headers->get('X-SESSION-ID');
        $content = $request->getContent();
        $arr = json_decode($content, true);
        $regid = $arr['regid'];
        $em = $this->getDoctrine()->getManager();

        $session = $em->getRepository('MegasoftEntangleBundle:Session')->findOneBy(array('sessionId' => $sessionid));

        if (!$session) {
            throw $this->createNotFoundException('no session found for session id = ' . $sessionid);
        }

        $qb = $em->createQueryBuilder();
        if($regid != null || $regid == ""){
            $qb->update('MegasoftEntangleBundle:Session','s')->set('s.expired',1)->where('s.regId =:regId AND s.sessionId != :sessionId')->setParameter('regId', $regid)->setParameter('sessionId',$sessionid)->getQuery()->execute();
            $em->flush();
        }
        $session->setRegId($regid);
        $em->flush();
        $response = new JsonResponse();
        $response->setData(array('status' => 'registered to GCM'));
        return $response;
    }

    function notificationStreamAction($userId)
    {
        $nc = $this->get('notification_center.service');
        $response = new JsonResponse();
        $response->setData();
        return $response;
    }

    /**
     * @return Response
     * @author amrelzanaty
     */
    public function SendEmailAction()
    {
        $message = "bate5a";
        $randomString = "thisIsARandomString";
        $title = "you are invited to bla";
        $body = "<!DOCTYPE html>
                <html lang=\"en\">
                    <head>
                    </head>
                    <body>
                           <h3>
                                Hello
                           </h3>
                           <p>" . $message . "</p>
                           <a href=\"http://entangle.io/invitation/" . $randomString . "\">link</a>
                           <p>Cheers<br>Entangle Team</p>
                    </body>
                </html>";

        $user = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail')->findOneBy(array("email" => "mohamed19936@gmail.com"))->getUser();
        $notificationCenter = $this->get('notification_center.service');
        $notificationCenter->sendMail($user->getId(), $title, $body);
        return new Response("OK", 200);
    }
}