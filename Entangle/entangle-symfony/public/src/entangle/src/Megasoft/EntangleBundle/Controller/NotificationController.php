<?php

/**
 * @author Mohamed Shaban
 *
 */


namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\NewMessageNotification;
use Megasoft\EntangleBundle\Entity\OfferChosenNotification;
use Megasoft\EntangleBundle\Entity\OfferDeletedNotification;
use Megasoft\EntangleBundle\Entity\PriceChangeNotification;
use Megasoft\EntangleBundle\Entity\RequestDeletedNotification;
use Megasoft\EntangleBundle\Entity\TransactionNotification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * this function is used to send notification for new message
     * @param $messageId
     * @return mixed
     */
    function newMessageNotification($messageId)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = new NewMessageNotification();
        $message = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Message')->find($messageId);
        $to = $message->getOffer()->getRequest()->getUserId();
        $from = $message->getSenderId();

        if ($from == $message->getOffer()->getUserId()) {
            $to = $message->getOffer()->getRequest()->getUserId();
        } else {
            $to = $message->getOffer()->getUserId();
        }

        $date = date('m/d/Y h:i:s a', time());
        $date = \DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setMessageId($messageId);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setUser($this->getDoctrine()->getRepository('MegasoftEntangleBundle:User')->find($to));
        $em->persist($notification);
        $em->flush();


        $fromName = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User')->find($from)->getName();
        $toName = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User')->find($to)->getName();

        $data = array('from' => $fromName, 'to' => $toName, 'message' => $message->getBody());
        return $this->notificationCenter($to, $data);

    }


    function transactionNotification($transactionid)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = new  TransactionNotification();
        $transaction = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:TransactionNotification')->find($transactionid);
        $to = $transaction->getUser();

        $from = $transaction->getOffer()->getUser()->getName();

        $date = date('m/d/Y h:i:s a', time());
        $date = \DateTime::createFromFormat('m/d/Y h:i:s a', $date);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setUser($to);
        $notification->setTransactionId($transactionid);
        $em->persist($notification);
        $em->flush();

        $amount = $transaction->getFinalPrice();
        $requestDesc = $transaction->getOffer()->getRequest()->getDescription();

        $data = array('from' => $from, 'amount' => $amount, 'requestDesc' => $requestDesc);

        return notficationCenter($to->getId(), $data);
    }

    /**
     *
     * @param $offerid
     * @param $oldPrice
     * @param $newPrice
     * @return bool|mixed
     */
    function offerChangeNotification($offerid, $oldPrice, $newPrice)
    {
        $em = $this->getDoctrine()->getManager();

        $offer = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $offer->getRequest();
        $notification = new PriceChangeNotification();

        $toName = $request->getUser()->getName();
        $fromName = $offer->getUser()->getName();
        $to = $request->getUserId();
        $date = date('m/d/Y h:i:s a', time());
        $date = \DateTime::createFromFormat('m/d/Y h:i:s a', $date);
        $user = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User')->find($to);

        $notification->setUser($user);
        $notification->setCreated($date);
        $notification->setSeen(false);
        $notification->setNewPrice($newPrice);
        $notification->setOldPrice($oldPrice);
        $notification->setOffer($offer);
        $em->persist($notification);
        $em->flush();

        $data = array('to' => $toName, 'from' => $fromName, 'newPrice' => $newPrice,);

        return $this->notificationCenter($to, $data);
    }

    function chooseOfferNotification($offerid)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = new OfferChosenNotification();
        $offer = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request')->find($offer->getRequestId());
        $to = $request->getUser();

        $date = date('m/d/Y h:i:s a', time());
        $date = \DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setUser($to);
        $notification->setSeen(false);
        $notification->setOffer($offer);

        $em->persist($notification);
        $em->flush();
        $data = array('');

        return $this->notificationCenter($to->getId(), $data);
    }


    // waiting for the next migrations

    function newOfferNotfication($offerid)
    {

    }


    function offerDeletedNotification($offerid)
    {
        $em = $this->getDoctrine()->getManager();
        $notification = new OfferDeletedNotification();

        $offer = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer')->find($offerid);
        $request = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request')->find($offer->getRequestId());
        $to = $request->getUser();

        $date = date('m/d/Y h:i:s a', time());
        $date = \DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        $notification->setCreated($date);
        $notification->setUser($to);
        $notification->setSeen(false);
        $notification->setOffer($offer);

        $em->persist($notification);
        $em->flush();
        $data = array('');

        return $this->notificationCenter($to->getId(), $data);

    }

    function requestDeletedNotification($requestid)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request')->find($requestid);
        $offers = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer')->findBy(array('reqestid' => $requestid));

        $date = date('m/d/Y h:i:s a', time());
        $date = \DateTime::createFromFormat('m/d/Y h:i:s a', $date);

        foreach ($offers as $offer) {
            $notification = new RequestDeletedNotification();
            $notification->setSeen(false);
            $notification->setCreated($date);
            $notification->setRequest($request);
            $notification->setUser($offer->getUser());

            $em->persist($notification);
            $em->flush();

            $data = array('');
            $this->notificationCenter($offer->getUserId(), $data);
        }
    }

    /**
     * this is a test action just to test notification center function
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function testAction()
    {
        $name = $this->newMessageNotification(0);
        $arr = array('regid' => $name,);
        return $this->render('MegasoftEntangleBundle:Default:test.html.twig', $arr);
    }

    /**
     * this searches for session ID and adds a new regId to this session
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     */
    function registerAction(Request $request)
    {
        $sessionid = $request->headers->get('X-SESSION-ID');
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
        $response = new JsonResponse();
        $response->setData(array('status' => 'registered to GCM'));
        return $response;
    }
}
