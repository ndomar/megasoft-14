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
            $array = array($description , $notification->getId() , $notification->getCreated() ,
                $notification->getSeen() , "claim=".$notification->getClaimId());
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
            $array = array($description , $notification->getId() , $notification->getCreated() ,
                $notification->getSeen() , "offer=".$notification->getOfferId());
            array_push($this->arr , $array);
        }
    }


}