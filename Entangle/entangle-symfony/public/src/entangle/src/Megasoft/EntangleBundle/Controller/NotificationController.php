<?php

/**
 * @author Mohamed Shaban
 *
 */


namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{

    /**
     * this is a test action just to test notification center function
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function testAction()
    {
        $nc = $this->get('notification_center.service');
        $response = new JsonResponse();
        $name = $nc->offerDeletedNotification(0);
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
        $session->setRegId($regid);
        $em->flush();
        $response = new JsonResponse();
        $response->setData(array('status' => 'registered to GCM'));
        return $response;
    }

}