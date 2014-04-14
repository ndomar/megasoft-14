<?php

use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
	public function setNotificationSeen(Request $request , $notificationId)
	{
		$sessionId = $request->headers->get('SessionID');
		$json = json_decode($request->getcontent() , true);
        
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Notification');
        $notification = $notificationRepo->findOneById($notificationId);
        
        if($notification == null)
        {
            return new Response("Notification is not found" ,404);
        }
        
        if( $session == null )
        {
            return new Response("Unauthorized",401);
        }
        
        $seen = $json['seen'];
        $notification->setSeen($seen);

        return new Response(200);
	}
}