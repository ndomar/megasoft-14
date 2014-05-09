<?php

/**
* @author : Mohamed Ayman
*
*/

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationStreamController extends Controller
{
	public function getNotificationAction($userId)
	{
		$notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Notification');
		$notifications = $notificationRepo->findBy(array('userId' => $userId));
		if(count($notifications) == 0)
		{
			return new Response("No notifications found" , 400);
		}
        $count = count($notifications);
        $arr = array();
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            array_push($arr , $notification->getId());
        }

		$response = new JsonResponse();
		$response->setdata($arr);
		$response->setStatusCode(200);
		return $response;
	}
}