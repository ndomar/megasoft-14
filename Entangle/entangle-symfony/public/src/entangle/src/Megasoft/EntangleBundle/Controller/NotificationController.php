<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{

    /**
     * Notification Center
     * this function is the function responsible for connecto to Google Cloud Messaging.
     * sending the notification to it
     * @param $userID
     * @param $notification
     * @return bool|mixed
     */
    function notificationCenter($userID, $notification)
    {
        $Authorization = $this->container->getParameter('GOOGLE_API_KEY');
        $serverUrl = $this->container->getParameter('SERVER_URL');

        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $result = $repo->findBy(array('userId' => $userID, 'expired' => 0,));

        if (!$result)
            return false;

        $regIdArray = array();
        foreach ($result as $id)
            array_push($regIdArray, $id->getRegId());


        $header = array('Authorization:key=' . $Authorization, 'Content-Type: application/json');
        $body = array("registration_ids" => $regIdArray,
            "notification" => $notification,
        );

        $request = curl_init($serverUrl);
        curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($request);
        return $result;
    }

    /**
     * this is a test action just to test notification center function
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function testAction()
    {
        $userID = 1;
        $notification = array('data' => 'hello world');
        $name = $this->notificationCenter($userID, $notification);
//        $name = ($name) ? "true" : "false";
        $arr = array('name' => $name,);
        return $this->render('MegasoftEntangleBundle:Default:test.html.twig', $arr);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function registerAction(Request $request)
    {
        $sessionid = $request->headers->get('sessionid');
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
        return $this->render('MegasoftEntangleBundle:Default:test.html.twig', $arr);
    }
}
