<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{

    function notificationCenter($userID, $notification)
    {
        $googleApiKey = $this->container->getParameter('GOOGLE_API_KEY');
        $regid = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User')->find($userID);
        

    }
}
