<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


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

        $regid = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session')->find($userID)->getsessionID();
        if (!$regid)
            return false;

        $Authorization = $this->container->getParameter('GOOGLE_API_KEY');
        $serverUrl = $this->container->getParameter('SERVER_URL');

        $header = array('Authorization:key=' + $Authorization, 'Content-Type: application/json');
        $body = array("registration_ids" => $regid,
            "notification" => $notification,
        );
        $request = curl_init($serverUrl);
        curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($request);
        return $result;
    }


    function testAction($name)
    {

    }
}
