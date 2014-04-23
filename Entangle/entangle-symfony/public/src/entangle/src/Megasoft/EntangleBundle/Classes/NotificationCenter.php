<?php
/**
 * User: mohamed shaban
 * Date: 4/24/14
 * Time: 12:06 AM
 */

namespace Megasoft\EntangleBundle\Classes;

use DateTime;
use Doctrine\ORM\EntityManager;
use Megasoft\EntangleBundle\Entity\NewMessageNotification;
use Megasoft\EntangleBundle\Entity\OfferChosenNotification;
use Megasoft\EntangleBundle\Entity\OfferDeletedNotification;
use Megasoft\EntangleBundle\Entity\PriceChangeNotification;
use Megasoft\EntangleBundle\Entity\RequestDeletedNotification;
use Megasoft\EntangleBundle\Entity\TransactionNotification;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;


class NotificationCenter
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;


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
     * this function is used to send notification for new message
     * @param $messageId
     * @param null $message
     * @return bool|mixed|JsonResponse
     */
    function newMessageNotification($messageId, $message = null)
    {

        $notification = new NewMessageNotification();
        $message = $this->em->getRepository('MegasoftEntangleBundle:Message')->find($messageId);
        $from = $message->getSenderId();

        if ($from == $message->getOffer()->getUserId()) {
            $to = $message->getOffer()->getRequest()->getUserId();
        } else {
            $to = $message->getOffer()->getUserId();
        }

        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setMessageId($messageId);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setUser($this->em->getRepository('MegasoftEntangleBundle:User')->find($to));

        $this->em->persist($notification);
        $this->em->flush();

        $fromName = $this->em->getRepository('MegasoftEntangleBundle:User')->find($from)->getName();
        $toName = $this->em->getRepository('MegasoftEntangleBundle:User')->find($to)->getName();

        $data = array('from' => $fromName, 'to' => $toName, 'message' => $message->getBody());
        return $this->notificationCenter($to, $data);

    }


    /**
     * @param $transactionid
     * @param null $message
     * @return bool|mixed
     */
    function transactionNotification($transactionid, $message = null)
    {
        $notification = new  TransactionNotification();
        $transaction = $this->em->getRepository('MegasoftEntangleBundle:TransactionNotification')->find($transactionid);
        $to = $transaction->getUser();

        $from = $transaction->getTransaction()->getOffer()->getUser()->getName();

        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setUser($to);
        $notification->setTransactionId($transactionid);
        $this->em->persist($notification);
        $this->em->flush();

        $amount = $transaction->getTransaction()->getFinalPrice();
        $requestDesc = $transaction->getTransaction()->getOffer()->getRequest()->getDescription();

        $data = array('from' => $from, 'amount' => $amount, 'requestDesc' => $requestDesc);

        return $this->notificationCenter($to->getId(), $data);
    }

    /**
     * @param $offerid
     * @param $oldPrice
     * @param $newPrice
     * @param null $message
     * @return bool|mixed
     */
    function offerChangeNotification($offerid, $oldPrice, $newPrice, $message = null)
    {

        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $offer->getRequest();
        $notification = new PriceChangeNotification();

        $toName = $request->getUser()->getName();
        $fromName = $offer->getUser()->getName();
        $to = $request->getUserId();
        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);
        $user = $this->em->getRepository('MegasoftEntangleBundle:User')->find($to);

        $notification->setUser($user);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setNewPrice($newPrice);
        $notification->setOldPrice($oldPrice);
        $notification->setOffer($offer);
        $this->em->persist($notification);
        $this->em->flush();

        $data = array('to' => $toName, 'from' => $fromName, 'newPrice' => $newPrice,);

        return $this->notificationCenter($to, $data);
    }

    /**
     * @param $offerid
     * @param null $message
     * @return bool|mixed
     */
    function chooseOfferNotification($offerid, $message = null)
    {
        $notification = new OfferChosenNotification();
        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $this->em->getRepository('MegasoftEntangleBundle:Request')->find($offer->getRequestId());
        $to = $request->getUser();

        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setUser($to);
        $notification->setSeen(false);
        $notification->setOffer($offer);

        $this->em->persist($notification);
        $this->em->flush();
        $data = array('');

        return $this->notificationCenter($to->getId(), $data);
    }


    // waiting for the next migrations

    function newOfferNotfication($offerid, $message = null)
    {

    }


    /**
     * @param $offerid
     * @param null $message
     * @return bool|mixed
     */
    function offerDeletedNotification($offerid, $message = null)
    {
        $notification = new OfferDeletedNotification();

        $offer = $this->em->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $this->em->getRepository('MegasoftEntangleBundle:Request')->find($offer->getRequestId());
        $to = $request->getUser();

        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setUser($to);
        $notification->setSeen(false);
        $notification->setOffer($offer);

        $this->em->persist($notification);
        $this->em->flush();
        $data = array('');

        return $this->notificationCenter($to->getId(), $data);

    }


    /**
     * @param $requestid
     * @param null $message
     */
    function requestDeletedNotification($requestid, $message = null)
    {

        $request = $this->em->getRepository('MegasoftEntangleBundle:Request')->find($requestid);
        $offers = $this->em->getRepository('MegasoftEntangleBundle:Offer')->findBy(array('reqestid' => $requestid));

        $date = date('m/d/Y h:i:s a', time());
        $date = DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        foreach ($offers as $offer) {
            $notification = new RequestDeletedNotification();
            $notification->setSeen(false);
            $notification->setCreated($date);
            $notification->setRequest($request);
            $notification->setUser($offer->getUser());

            $this->em->persist($notification);
            $this->em->flush();

            $data = array('');
            $this->notificationCenter($offer->getUserId(), $data);
        }
    }
}