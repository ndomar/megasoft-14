<?php

/**
 * Gets all the notifications of a user
* @author : Mohamed Ayman
*
*/

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class NotificationStreamController
 * @package Megasoft\EntangleBundle\Controller
 * This controller retrieves the notifications of a user from the database using doctrine
 * @author : Mohamed Ayman
 */
class NotificationStreamController extends Controller
{
    //The array that will have all the notifications
    private $arr;

    /**
     * @param $userId
     * @return The name of the user
     * @author : Mohamed Ayman
     */
    public function getName($userId)
    {
        $userRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        $user = $userRepo->findOneById($userId);

        return $user->getName();
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getClaimNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:NewClaimNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $claimerId = $notification->getClaim()->getClaimerId();
            $claimerName = $this->getName($claimerId);
            $description = $claimerName." has claimed you!";
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "claim=".$notification->getClaimId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getPriceChangeNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PriceChangeNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $offererId = $notification->getOffer()->getUserId();
            $offererName = $this->getName($offererId);
            $description = $offererName." has changed the price to ".$notification->getNewPrice();
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "offer=".$notification->getOfferId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getNewOfferNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:NewOfferNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $offererId = $notification->getOffer()->getUserId();
            $offererName = $this->getName($offererId);
            $description = "You got a new offer from ".$offererName;
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "offer=".$notification->getOfferId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getReopenRequestNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:ReopenRequestNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $otherUserId = $notification->getRequest()->getUserId();
            $name = $this->getName($otherUserId);
            $description = $name." has re-opened his request!";
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "request=".$notification->getRequestId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getOfferChosenNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:OfferChosenNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $otherUserId = $notification->getOffer()->getUserId();
            $name = $this->getName($otherUserId);
            $description = $name." has chosen your offer!";
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "offer=".$notification->getOfferId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getRequestDeletedNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:RequestDeletedNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $otherUserId = $notification->getRequest()->getUserId();
            $name = $this->getName($otherUserId);
            $description = $name." has deleted his request!";
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "request=".$notification->getRequestId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getNewMessageNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:NewMessageNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $description = "You have a new message!";
            $message = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Message')
                ->findOneById($notification->getMessageId());
            $offerId = $message->getOfferId();
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "offer=".$offerId ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getTransactionNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:TransactionNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $description = "You have a new transaction!";
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "offer=".$notification->getTransaction()->getOfferId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * Adds the Notifications into the array
     * @author : Mohamed Ayman
     */
    public function getOfferDeletedNotifications($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:OfferDeletedNotification');
        $notifications = $notificationRepo->findBy(array('userId' => $userId));
        if(count($notifications) == 0)
        {
            return;
        }
        $count = count($notifications);
        for($i = 0; $i < $count ; $i++)
        {
            $notification = $notifications[$i];
            $otherUserId = $notification->getOffer()->getUserId();
            $name = $this->getName($otherUserId);
            $description = $name." has deleted his offer!";
            $array = array($description , $notification->getId() , $notification->getCreated()->format('m-d H:i') ,
                $notification->getSeen() , "offer=".$notification->getOfferId() ,);
            array_push($this->arr , $array);
        }
    }

    /**
     * @param $userId
     * @return JsonResponse
     * Gets all the notifications of the user
     * @author : Mohamed Ayman
     */
    public function getNotificationAction($userId)
	{
        $this->arr = array();
        $this->getClaimNotifications($userId);
        $this->getPriceChangeNotifications($userId);
        $this->getNewOfferNotifications($userId);
        $this->getReopenRequestNotifications($userId);
        $this->getOfferChosenNotifications($userId);
        $this->getRequestDeletedNotifications($userId);
        $this->getNewMessageNotifications($userId);
        $this->getTransactionNotifications($userId);
        $this->getOfferDeletedNotifications($userId);
        usort($this->arr , array($this , "notification_compare"));
		$response = new JsonResponse();
		$response->setdata($this->arr);
		$response->setStatusCode(200);

		return $response;
	}

    /**
     * @param $a
     * @param $b
     * @return int
     * This function to sort the array
     */
    function notification_compare($a , $b)
    {
        return strnatcasecmp($b[1] , $a[1]);
    }

    /**
     * @param $notificationId
     * @return Response
     * Sets a notification status as seen
     * @author : Mohamed Ayman
     */
    public function setSeenAction($notificationId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Notification');
        $notification = $notificationRepo->findOneById($notificationId);
        $notification->setSeen(true);
        $this->getDoctrine()->getManager()->persist($notification);
        $this->getDoctrine()->getManager()->flush();

        return new Response("Ok" , 200);
    }

    /**
     * @param $userId
     * @return JsonResponse
     * Get the number of new notifications
     * @author Mohamed Ayman
     */
    public function notificationCountAction($userId)
    {
        $notificationRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Notification');
        $notification = $notificationRepo->findBy(array('userId' => $userId , 'seen' => false));
        $count = count($notification);
        $response = new JsonResponse();
        $response->setData(array('count' => $count));
        $response->setStatusCode(200);

        return $response;
    }

}