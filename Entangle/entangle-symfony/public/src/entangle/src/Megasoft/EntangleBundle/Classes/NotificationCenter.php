<?php
/**
 * User: mohamed shaban
 * Date: 4/24/14
 * Time: 12:06 AM
 */

namespace Megasoft\EntangleBundle\Classes;

use DateTime;
use Doctrine\ORM\EntityManager;
use Megasoft\EntangleBundle\Entity\NewClaimNotification;
use Megasoft\EntangleBundle\Entity\NewMessageNotification;
use Megasoft\EntangleBundle\Entity\NewOfferNotification;
use Megasoft\EntangleBundle\Entity\OfferChosenNotification;
use Megasoft\EntangleBundle\Entity\OfferDeletedNotification;
use Megasoft\EntangleBundle\Entity\PriceChangeNotification;
use Megasoft\EntangleBundle\Entity\ReopenRequestNotification;
use Megasoft\EntangleBundle\Entity\RequestDeletedNotification;
use Megasoft\EntangleBundle\Entity\TransactionNotification;
use Symfony\Component\DependencyInjection\Container;


/**
 * this is the main part of notification system
 * Class NotificationCenter
 * @package Megasoft\EntangleBundle\Classes
 */
class NotificationCenter
{
    /**
     * Entity Manager for Notification Center
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Symfony Container for Notification Center
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * Default new message notification title
     * @var string
     */
    private $newMessageDefaultTitle = "new message notification";

    /**
     * default transaction notification title
     * @var string
     */
    private $transactionNotificationDefaultTitle = "new transaction notification";

    /**
     * default offer change notification title
     * @var string
     */
    private $offerChangeNotificationDefaultTitle = "offer change notification";

    /**
     * default offer chosen notification title
     * @var string
     */
    private $offerChosenNotificationDefaultTitle = "offer chosen notification";

    /**
     * default new offer notification title
     * @var string
     */
    private $newOfferNotificationTitle = "new offer Notification";

    /**
     * default offer deleted notification title
     * @var string
     */
    private $offerDeletedNotificationDefaultTitle = "offer deleted notification";

    /**
     * default request deleted notification title
     * @var string
     */
    private $requestDeletedNotificationDefaultTitle = "request deleted notification";

    /**
     * default request reopen notification
     * @var string
     */
    private $reopenRequestNotificationDefaultTitle = "request reopened notification";

    /**
     * default new claim notification title
     * @var string
     */
    private $newClaimNotificationDefaultTitle = "new claim notification";

    /**
     * new message notification ID
     * @var int
     */
    private $newMessageNotificationId = 0;

    /**
     * transaction notification ID
     * @var int
     */
    private $transactionNotificationId = 1;

    /**
     * offer change notification ID
     * @var int
     */
    private $offerChangeNotificationId = 2;

    /**
     * offer chosen notification ID
     * @var int
     */
    private $offerChosenNotificationId = 3;

    /**
     * new offer notification ID
     * @var int
     */
    private $newOfferNotificationId = 4;

    /**
     * offer deleted notification ID
     * @var int
     */
    private $offerDeletedNotificationId = 5;

    /**
     * request deleted notification Id
     * @var int
     */
    private $requestDeletedNotificationId = 6;

    /**
     * request reopen notification id
     * @var int
     */
    private $reopenRequestNotificationId = 7;

    /**
     * new claim notification id
     * @var int
     */
    private $newClaimNotificationId = 8;


    /**
     * @param EntityManager $em
     * @param Container $container
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }


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

        $repo = $this->em->getRepository('MegasoftEntangleBundle:Session');
        $result = $repo->findBy(array('userId' => $userID, 'expired' => 0,));

        if (!$result)
            return array('success' => 0);

        $regIdArray = array();
        foreach ($result as $id)
            array_push($regIdArray, $id->getRegId());


        $header = array('Authorization:key=' . $Authorization, 'Content-Type: application/json');
        $body = array("registration_ids" => $regIdArray,
            "data" => $notification,
        );

        $request = curl_init($serverUrl);
        curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($request);
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * this function is used to send notification for new message
     * data array ("title"=> notification title, "body" => notification body,"offerId"=>offer id)
     * @param $messageId
     * @param null $body
     * @param null $title
     * @return bool|mixed
     */
    function newMessageNotification($messageId, $title = null, $body = null)
    {

        $notification = new NewMessageNotification();
        $message = $this->em->getRepository('MegasoftEntangleBundle:Message')->find($messageId);
        $from = $message->getSender();
        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);
        if ($from->getId() == $message->getOffer()->getUserId())
            $to = $message->getOffer()->getRequest()->getUser();
        else
            $to = $message->getOffer()->getUser();

        $notification->setMessage($message);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setUser($this->em->getRepository('MegasoftEntangleBundle:User')->find($to));
        $this->em->persist($notification);
        $this->em->flush();

        $fromName = $from->getName();
        $toName = $to->getName();
        if (!$title)
            $title = $this->newMessageDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $fromName, $toName);
        else
            $body = "new Message from " . $fromName;

        $data = array('notificationId' => $notification->getId(), 'title' => $title, 'body' => $body, 'type' => $this->newMessageNotificationId, "offerId" => $message->getOffer()->getId());
        return $this->notificationCenter($to->getId(), $data);

    }


    /**
     * this is invoked when there is a requester accepts an offer
     * data array ("title"=> notification title, "body" => notification body, "by"=>requester from,
     * "requestDesc" => Description of request, "finalPrice" => price of final offer)
     * @param $transactionId
     * @param null $title
     * @param null $body
     * @return bool|mixed
     */
    function transactionNotification($transactionId, $title = null, $body = null)
    {
        $notification = new  TransactionNotification();
        $transaction = $this->em->getRepository('MegasoftEntangleBundle:Transaction')->find($transactionId);
        $to = $transaction->getOffer()->getUser();
        $from = $transaction->getOffer()->getRequest()->getUser();
        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setUser($to);
        $notification->setTransaction($transaction);
        $this->em->persist($notification);
        $this->em->flush();

        $finalPrice = $transaction->getFinalPrice();
        $requestDesc = $transaction->getOffer()->getRequest()->getDescription();
        $fromName = $from->getName();
        $toName = $to->getName();
        if (!$title)
            $title = $this->transactionNotificationDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $fromName, $toName);
        else
            $body = $fromName . "accepted your offer";

        $data = array('notificationId' => $notification->getId(), 'title' => $title, 'body' => $body, 'type' => $this->transactionNotificationId, 'by' => $fromName,
            'finalPrice' => $finalPrice, 'requestDesc' => $requestDesc, 'transactionId' => $transactionId);
        return $this->notificationCenter($to->getId(), $data);
    }

    /**
     *  this notifies the requester that an offer changed
     * data array ("title"=> notification title, "body" => notification body, "offerId"=>offer Id)
     * @param $offerId
     * @param $oldPrice
     * @param null $title
     * @param null $body
     * @return bool|mixed
     */
    function offerChangeNotification($offerId, $oldPrice, $title = null, $body = null)
    {

        $notification = new PriceChangeNotification();
        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerId);
        $request = $offer->getRequest();
        $from = $offer->getUser();
        $to = $request->getUser();
        $newPrice = $offer->getRequestedPrice();
        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);


        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setNewPrice($newPrice);
        $notification->setOldPrice($oldPrice);
        $notification->setOffer($offer);
        $notification->setUser($to);
        $this->em->persist($notification);
        $this->em->flush();


        $fromName = $from->getName();
        $toName = $to->getName();
        if (!$title)
            $title = $this->offerChangeNotificationDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $fromName, $toName);
        else
            $body = $fromName . "changed his offer";
        $data = array('notificationId' => $notification->getId(), 'title' => $title, 'body' => $body, 'type' => $this->offerChangeNotificationId, 'offerId' => $offerId);
        return $this->notificationCenter($to->getId(), $data);
    }

    /**
     * this notifies the offerer that his offer was chosen
     * data array ("title"=> notification title, "body" => notification body, "offerId" =>offerId)
     * @param $offerId
     * @param null $title
     * @param null $body
     * @return bool|mixed
     */
    function offerChosenNotification($offerId, $title = null, $body = null)
    {
        $notification = new OfferChosenNotification();
        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerId);
        $request = $offer->getRequest();
        $to = $offer->getUser();
        $from = $request->getUser();
        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setUser($to);
        $notification->setSeen(false);
        $notification->setOffer($offer);
        $this->em->persist($notification);
        $this->em->flush();

        $tangleId = $request->getTangle()->getId();

        $fromName = $from->getName();
        $toName = $to->getName();
        if (!$title)
            $title = $this->offerChosenNotificationDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $fromName, $toName);
        else
            $body = $fromName . " chose your offer";

        $data = array('notificationId' => $notification->getId(), 'tangleName' => $request->getTangle()->getName(), 'tangleId' => $tangleId, 'title' => $title, 'body' => $body, 'type' => $this->offerChosenNotificationId,
            'requestId' => $request->getId());
        return $this->notificationCenter($to->getId(), $data);
    }


    /**
     * data array ("title"=> notification title, "body" => notification body,"type"=>new offer notification id "offerId"=>offerId,)
     * @param $offerid
     * @param null $title
     * @param null $body
     * @return bool|mixed
     */

    function newOfferNotification($offerid, $title = null, $body = null)
    {
        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $offer->getRequest();
        $notification = new NewOfferNotification();
        $from = $offer->getUser();

        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setSeen(false);
        $notification->setOffer($offer);
        $notification->setUser($request->getUser());
        $notification->setCreated($date);

        $this->em->persist($notification);
        $this->em->flush();

        if (!$title)
            $title = $this->newOfferNotificationTitle;
        if ($body)
            $body = $this->formatMessage($body, $from, null);
        else
            $body = $from->getName() . "made a new offer";

        $data = array('notificationId' => $notification->getId(), "title" => $title, "body" => $body, "type" => $this->newOfferNotificationId, "offerId" => $offerid);
        return $this->notificationCenter($from->getId(), $data);

    }


    /**
     *  this should be invoked when a user delete an offer
     * data array ("title"=> notification title, "body" => notification body, "offerId"=>offerId,)
     * @param $offerId
     * @param null $title
     * @param null $body
     * @return bool|mixed
     */
    function offerDeletedNotification($offerId, $title = null, $body = null)
    {
        $notification = new OfferDeletedNotification();
        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerId);
        $request = $this->em->getRepository('MegasoftEntangleBundle:Request')->find($offer->getRequestId());
        $to = $request->getUser();
        $from = $offer->getUser();
        $date = date('m / d / Y h:i:s a', time());
        $date = DateTime::createFromFormat('m / d / Y h:i:s a', $date);


        $notification->setCreated($date);
        $notification->setUser($to);
        $notification->setSeen(false);
        $notification->setOffer($offer);
        $this->em->persist($notification);
        $this->em->flush();


        $fromName = $from->getName();
        $toName = $to->getName();
        if (!$title)
            $title = $this->offerDeletedNotificationDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $fromName, $toName);
        else
            $body = $fromName . "deleted his offer";

        $data = array('notificationId' => $notification->getId(), 'title' => $title, 'body' => $body, 'type' => $this->offerDeletedNotificationId,
            'offerId' => $offerId, 'requestId' => $request->getId());
        return $this->notificationCenter($to->getId(), $data);

    }


    /**
     * this should be invoked when a user delete a request
     * data array ("title"=> notification title, "body" => notification body,"requestId"=>requestId)
     * @param $requestId
     * @param null $title
     * @param null $body
     */
    function requestDeletedNotification($requestId, $title = null, $body = null)
    {

        $request = $this->em->getRepository('MegasoftEntangleBundle:Request')->find($requestId);
        $offerArray = $this->em->getRepository('MegasoftEntangleBundle:Offer')->findBy(array('requestId' => $requestId));
        $from = $request->getUser();

        $date = date('m / d / Y h:i:s a', time());
        $date = DateTime::createFromFormat('m / d / Y h:i:s a', $date);

        foreach ($offerArray as $offer) {
            $to = $offer->getUser();

            $notification = new RequestDeletedNotification();
            $notification->setSeen(false);
            $notification->setCreated($date);
            $notification->setRequest($request);
            $notification->setUser($offer->getUser());

            $this->em->persist($notification);
            $this->em->flush();

            $fromName = $from->getName();
            $toName = $to->getName();
            if (!$title)
                $title = $this->requestDeletedNotificationDefaultTitle;
            if ($body)
                $body = $this->formatMessage($body, $fromName, $toName);
            else
                $body = $fromName . "deleted his request";

            $data = array('notificationId' => $notification->getId(), 'title' => $title, 'body' => $body, 'type' => $this->requestDeletedNotificationId,
                'requestId' => $requestId, 'tangleId' => $request->getTangle()->getId());
            $this->notificationCenter($offer->getUserId(), $data);
        }
    }

    /**
     * this notification should be used when a request is reopened
     * data array ("title" =>title, "body"=>notification body, "request id" => request id)
     * @param $requestId
     * @param null $title
     * @param null $body
     */
    function reopenRequestNotification($requestId, $title = null, $body = null)
    {
        $request = $this->em->getRepository("MegasoftEntangleBundle:Request")->find($requestId);
        $offers = $this->em->getRepository("MegasoftEntangleBundle:Offer")->findBy(array("requestId" => $requestId));

        $date = date('m / d / Y h:i:s a', time());
        $date = DateTime::createFromFormat('m / d / Y h:i:s a', $date);

        $fromName = $request->getUser()->getName();
        if (!$title)
            $title = $this->reopenRequestNotificationDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $fromName, null);
        else
            $body = $fromName . "reopened his request";

        $data = array("title" => $title, "body" => $body, "type" => $this->reopenRequestNotificationId, "requestId" => $requestId);

        foreach ($offers as $offer) {
            $notification = new ReopenRequestNotification();
            $data['notificationId'] = $notification->getId();
            $notification->setSeen(false);
            $notification->setRequest($request);
            $notification->setCreated($date);
            $notification->setUser($offer->getUser());
            $this->notificationCenter($offer->getUser()->getId(), $data);
        }
    }


    /**
     *
     * @param $claimId
     * @param null $title
     * @param null $body
     */
    function newClaimNotification($claimId, $title = null, $body = null)
    {
        $claim = $this->em->getRepository("MegasoftEntangleBundle:Claim")->find($claimId);
        $notification = new NewClaimNotification();
        $claimer = $claim->getClaimer();
        $tangle = $claim->getTangle();
        $tangleOwner = $this->em->getRepository("MegasoftEntangleBundle:UserTangle")->findOneBy(array("tangleId" => $tangle->getId(), "tangleOwner" => true));

        if ($claimer->getId() == $claim->getOffer()->getUser()->getId())
            $claimed = $claim->getOffer()->getRequest()->getUser();
        else
            $claimed = $claim->getOffer()->getUser();

        $date = date('m / d / Y h:i:s a', time());
        $date = DateTime::createFromFormat('m / d / Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setClaim($claim);
        $notification->setCreated($date);
        $notification->setUser($claimed);

        if (!$title)
            $title = $this->newClaimNotificationDefaultTitle;
        if ($body)
            $body = $this->formatMessage($body, $claimer->getName(), null);
        else
            $body = $claimer->getName() . "claimed you ";

        $data = array('notificationId' => $notification->getId(), "title" => $title, "body" => $body, "type" => $this->newClaimNotificationId, "claimId" => $claimId);
        $this->notificationCenter($claim->getId(), $data);
        $this->notificationCenter($tangleOwner->getUser()->getId(), $data);
    }

    // waiting for new migrations
    function markOfferDoneNotification($offerId)
    {

    }

    /**
     * this should be used to format user messages
     * currently it's for to and from only
     * @param $message
     * @param $from
     * @param $to
     * @return mixed
     */
    function formatMessage($message, $from, $to)
    {
        if ($from)
            $message = str_replace("{{from}}", $from, $message);
        if ($to)
            $message = str_replace("{{to}}", $to, $message);
        return $message;
    }

}
