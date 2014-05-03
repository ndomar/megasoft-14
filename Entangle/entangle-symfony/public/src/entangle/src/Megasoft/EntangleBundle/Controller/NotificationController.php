<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends Controller
{
    /**
     * This checks if the notification status seen or not
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $notificationId
     * @return the status of notification
     * @author Mohamed Ayman
     */
    public function getSeenAction(Request $request , $notificationId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');
        if( $sessionId == null )
        {
            return new Response("Unauthorized",401);
        }
        
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Notification');
        $notification = $notificationRepo->findOneById($notificationId);
        
        if(!$notification)
        {
            return new Response("Notification is not found" ,400);
        }
          
        $seen = $notification->getSeen();
        $response = new JsonResponse();
        $response->setdata(array('seen'=>$seen));
        $response->setStatusCode(200);
        return $response;
    }
    
    
}