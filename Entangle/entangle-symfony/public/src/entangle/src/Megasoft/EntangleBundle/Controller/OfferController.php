<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Tests\String;
use Symfony\Component\HttpFoundation\JsonResponse;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\Message;
use Megasoft\EntangleBundle\Entity\user;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Transaction;

/**
 * Gets the required information to view a certain offer
 * @author Almgohar
 */
class OfferController extends Controller {

    /**
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @param integer $sessionId
     * @return boolean true if the user can view this request and false otherwise
     * @author Almgohar
     */
    private function validateUser($request, $sessionId) {
        $sessionTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:Session');
        $userTangleTable = $this->getDoctrine()->
                getRepository('MegasoftEntangleBundle:UserTangle');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        $loggedInUser = $session->getUserId();
        $tangleId = $request->getTangleId();

        $userTangle = $userTangleTable->
                findOneBy(array('userId' => $loggedInUser, 'tangleId' => $tangleId));

        if ($userTangle == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Sends the required information in a JSon response
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param integer $offerId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author Almgohar
     */
    public function offerAction
    (\Symfony\Component\HttpFoundation\Request $req, $offerId) {
        $sessionId = $req->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response('Please login again', 401);
        }
        $doctrine = $this->getDoctrine();
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Please login again', 401);
        }
        $offerTable = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerTable->findOneBy(array('id' => $offerId));
        if ($offer == null || $offer->getDeleted()) {
            return new Response('Offer not found', 404);
        }
        $request = $offer->getRequest();
        if ($request->getDeleted()) {
            return new Response("Request not found", 404);
        }
        $tangleId = $request->getTangleId();
        if (!$this->validateUser($request, $sessionId)) {
            return new Response('Unauthorized', 401);
        }
        $messageTable = $doctrine->getRepository('MegasoftEntangleBundle:Message');
        $comments = $this->getComments($messageTable, $offerId);
        $offerInformation = $this->getOfferInformation($offer);

        $response = new JsonResponse(null, 200);
        $response->setData(array('tangleId' => $tangleId,
            'offerInformation' => $offerInformation,
            'comments' => $comments,));
        return $response;
    }

    /**
     * Gets the comments of a certain offer
     * @param \Megasoft\EntangleBundle\Entity\Request $request $request
     * @return array $comments
     * @author Almgohar
     */
    private function getComments($messageTable, $offerId) {
        $comments = array();
        $messages = $messageTable->findBy(array('offerId' => $offerId));
        for ($i = 0; $i < count($messages); $i++) {
            $message = $messages[$i];
            if ($message == null) {
                continue;
            }
            $commenter = $message->getSender()->getName();
            $commentDate = $message->getDate()->format('d/m/Y');
            $comment = $message->getBody();
            $comments[] = array('commenter' => $commenter,
                'comment' => $comment,
                'commentDate' => $commentDate,);
        }

        return $comments;
    }

    /**
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offer
     * @return array $offerInformation
     * @author Almgohar
     */
    private function getOfferInformation($offer) {
        $user = $offer->getUser();
        $offererId = $user->getId();
        $requesterId = $offer->getRequest()->getUserId();
        $requestId = $offer->getRequestId();
        $userName = $user->getName();
        $offerDate = $offer->getDate()->format('d/m/Y');
        $userPhoto = $user->getPhoto();
        $offerStatus = $offer->getStatus();
        $offerPrice = $offer->getRequestedPrice();
        $offerDescription = $offer->getDescription();
        $offerDeadline = $offer->getExpectedDeadline()->format('d/m/Y');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $request = $sesionRepo->findOneBy(array('id' => $requestId));
        $requestStatus = $request->getStatus();
        $offerInformation = array('offererAvatar' => $userPhoto, 'offererName' => $userName,
            'offerDescription' => $offerDescription,
            'offerDeadline' => $offerDeadline,
            'offerStatus' => $offerStatus,
            'requesterId' => $requesterId,
            'offerPrice' => $offerPrice,
            'offererId' => $offererId,
            'offerDate' => $offerDate,
            'requestStatus' => $requestStatus,
        );

        return $offerInformation;
    }

    /**
     * Changes the price of an offer
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @param type $offerid
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Mansour
     */
    public function changeOfferPriceAction(Request $request, $offerid) {

        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($sessionId == null) {
            return new Response("Please login again", 400);
        }
        if ($session == null) {
            return new Response("Please login again", 401);
        }
        $sessionExpired = $session->getExpired();
        if ($sessionExpired) {
            return new Response("Please login again", 440);
        }
        $offerRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer');
        $requestOffer = $offerRepo->findOneBy(array('id' => $offerid));
        $oldPrice = $requestOffer->getRequestedPrice();
        if ($requestOffer == null) {
            return new Response("No such offer", 404);
        }
        if (($session->getUserId()) != ($requestOffer->getUserId())) {
            return new Response("You are unauthorized to change this offer", 401);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->ACCEPTED)) {
            return new Response("Offer has already been accepted", 403);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->DONE)) {
            return new Response("Offer has already been done", 403);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->FAILED)) {
            return new Response("Offer has already been failed", 403);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->REJECTED)) {
            return new Response("Offer has already been rejected", 403);
        }
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $newOfferPrice = $json_array['newPrice'];
        if ($newOfferPrice == null) {
            return new Response("Please enter a new price", 400);
        }
        if (($requestOffer->getRequestedPrice()) == $newOfferPrice) {
            return new Response("This is the same price, please enter a new price", 400);
        }
        $requestOffer->setRequestedPrice($newOfferPrice);

        //notification

// $notificationCenter = $this->get('notification_center.service');
// $title = "offer changed";
// $body = "{{from}} changed his offer";
// $notificationCenter->offerChangeNotification($requestOffer->getId(), $oldPrice, $title, $body);

        $notificationCenter = $this->get('notification_center.service');
        $title = "offer changed";
        $body = "{{from}} changed his offer";
        $notificationCenter->offerChangeNotification($requestOffer->getId(), $oldPrice, $title, $body);


        $this->getDoctrine()->getManager()->persist($requestOffer);
        $this->getDoctrine()->getManager()->flush();
        return new Response('Price changed successfully', 200);
    }

    /**
     * this recieves a request and calls verify to check if it can accept the offer
     * @param Request $request
     * @return Response $response returns 201 or 409 status code and message depending on verification
     * @author sak9
     */
    public function acceptOfferAction(\Symfony\Component\HttpFoundation\Request $request) {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return $response = new Response("Please login again", 400);
        }
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            return $response = new Response("Please login again", 400);
        }
        if ($session->getExpired() == 1) {
            return $response = new Response("Please login again", 401);
        }
        $userOfSession = $session->getUserId();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        if ($offerId == null) {
            return $response = new Response("Please select an offer", 400);
        }
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($offer == null) {
            return $response = new Response("No such offer exists", 404);
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId,));
        $requesterId = $request->getUserId();
        $tangle = $request->getTangleId();
        if ($requesterId != $userOfSession) {
            return $response = new Response("You are unauthorized to accept this offer", 409);
        }
        $verificationMessage = $this->verify($offerId);
        if ($verificationMessage == "Offer Accepted") {
            $response = new Response($verificationMessage, 201);
        } else {
            if ($verificationMessage == "You do not have enough balance to accept this offer") {
                $response = new Response($verificationMessage, 405);
            } else {
                $response = new Response($verificationMessage, 401);
            }
        }

        return $response;
    }

    /**
     * this recieves an offerId and checks if it can be accepted, if it can it accepts it and updates all fields in tables
     * @param Int $offerId
     * @return String either a success or error message
     * @author sak9
     */
    public function verify($offerId) {
        $doctrine = $this->getDoctrine();
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId,));
        if (count($offer) <= 0) {
            return "No such offer";
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId,));
        $requesterId = $request->getUserId();
        $tangleId = $request->getTangleId();
        $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requester = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $requesterId,));
        if (count($requester) <= 0) {
            return "You don't belong to this tangle";
        }
        if ($request->getDeleted() == 1) {
            return "This request has been deleted";
        }
        if ($request->getStatus() == $request->CLOSE) {
            return "This request has been closed";
        }
        if ($request->getStatus() == $request->FROZEN) {
            return "This request has been frozen";
        }
        if ($offer->getDeleted() == 1) {
            return "This offer has been deleted";
        }
        if ($offer->getStatus() == $offer->DONE || $offer->getStatus() == $offer->ACCEPTED) {
            return "This offer has already been accepted";
        }
        if ($offer->getStatus() == $offer->FAILED || $offer->getStatus() == $offer->REJECTED) {
            return "This offer has been failed or rejected";
        }
        $price = $offer->getRequestedPrice();
        $requesterBalance = $requester->getCredit();
        if ($requesterBalance + 100 < $price) {
            return "Error: Not enough balance.";
        }
        $request->setStatus($request->FROZEN);
        $requester->setCredit($requesterBalance - $price);
        $offer->setStatus($offer->ACCEPTED);
        $doctrine->getManager()->persist($request);
        $doctrine->getManager()->persist($requester);
        $doctrine->getManager()->persist($offer);
        $doctrine->getManager()->flush();

        // notification
        $notificationCenter = $this->get('notification_center.service');
        $title = "offer accepted";
        $body = "{{from}} accepted your offer";
        $notificationCenter->offerChosenNotification($offerId, $title, $body);

        return "Offer Accepted.";
    }

    /**
     * An endpoint to withdraw an offer.
     * @param Request $request
     * @param integer $offerId
     * @return Response
     * @author OmarElAzazy
     */
    public function withdrawAction(Request $request, $offerId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($offerId == null || $sessionId == null) {
            return new Response('Please login again', 400);
        }

        $doctrine = $this->getDoctrine();

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Please login again', 400);
        }

        $offererId = $session->getUserId();

        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($offer == null || $offer->getUserId() != $offererId || $offer->getDeleted()) {
            return new Response('You can not delete this offer', 401);
        }

        if ($offer->getStatus() == $offer->ACCEPTED) {
            $this->unfreezePoints($offer->getRequest(), $offer->getRequestedPrice());
        }

        // notification
        $notificationCenter = $this->get('notification_center.service');
        $title = "offer deleted";
        $body = "{{from}} deleted his offer";
        $notificationCenter->offerDeletedNotification($offer->getId(), $title, $body);


        $offer->setDeleted(true);
        $offer->setStatus($offer->FAILED);
        $this->getDoctrine()->getManager()->persist($offer);
        $this->getDoctrine()->getManager()->flush();

        return new Response("Deleted", 204);
    }

    /**
     * A function to unfreeze points for the requester for withdrawn offer.
     * @param Request $request
     * @param integer $points
     * @return
     * @author OmarElAzazy
     */
    public function unfreezePoints($request, $points) {
        $requesterId = $request->getUser()->getId();
        $tangleId = $request->getTangleId();

        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        $userTangle = $userTangleRepo->findOneBy(array('userId' => $requesterId, 'tangleId' => $tangleId));

        $newCredit = $userTangle->getCredit() + $points;
        $userTangle->setCredit($newCredit);

        $this->getDoctrine()->getManager()->persist($userTangle);
        $this->getDoctrine()->getManager()->flush();
        return;
    }

    /**
     * Validates the authority of the user
     * @param Request $request
     * @param integer $offerId
     * @return Response | null
     * @author MohamedBassem
     */
    private function verifyUser($request, $offerId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);
        if ($sessionId == null) {
            return new Response('Please login again', 400);
        }

        if ($offerId == null || $json['body'] == null) {
            return new Response('Please select an offer', 400);
        }

        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');

        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Please login again', 400);
        }

        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->find($offerId);

        $user = $session->getUser();

        $userId = $user->getId();
        $tangleId = $offer->getRequest()->getTangleId();

        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'userId' => $userId));


        if ($userTangle == null) {
            return new Response('You do not belong to this tangle', 401);
        }

        return null;
    }

    /**
     * The endpoint responsible for adding comments on offers
     * @param Request $request
     * @param $offerId
     * @return null|Response
     */
    public function commentAction(Request $request, $offerId) {
        $verification = $this->verifyUser($request, $offerId);

        if ($verification != null) {
            return $verification;
        }

        $doctrine = $this->getDoctrine();

        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);

        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->find($offerId);

        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');

        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));

        $user = $session->getUser();

        $message = new Message();
        $message->setOffer($offer);
        $message->setSender($user);
        $message->setDate(new \DateTime("now"));
        $message->setDeleted(false);
        $message->setBody($json['body']);

        $doctrine->getManager()->persist($message);
        $doctrine->getManager()->flush();

        return new Response('Ok', 201);
    }

    /**
     * This marks an offer as done
     * @param Int $offerid offer ID
     * @param \Symfony\Component\HttpFoundation\Request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author mohamedzayan
     */
    public function updateAction($offerid, \Symfony\Component\HttpFoundation\Request $request) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response('Please login again', 401);
        }
        $doctrine = $this->getDoctrine();
        $requestTable = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offerId = $offerid;
        $offer = $repo->find($offerId);
        $requestid = $offer->getRequestId();
        $testrequest = $requestTable->find($requestid);
        if ($testrequest == null) {
            return new Response('This request does not exist', 401);
        }
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Please login again', 401);
        }
        $userOfSession = $session->getUserId();
        if ($testrequest->getDeleted()) {
            return new Response('This request does not exist anymore', 401);
        }
        if ($testrequest->getStatus() == $testrequest->CLOSE) {
            return new Response('This request has been closed', 401);
        }
        if ($offer == null) {
            return new Response('This offer does not exist', 401);
        }
        if ($testrequest->getId() != $offer->getRequest()->getId()) {
            return new Response('You are unauthorized to perform this action', 401);
        }
        $status = $offer->DONE;
        $request = $offer->getRequest();
        $requesterId = $request->getUserId();
        if ($requesterId != $userOfSession) {
            return new Response('You are unauthorized to perform this action', 409);
        }
        $backendstatus = $offer->getStatus();
        if ($backendstatus == $offer->DONE) {
            return new JsonResponse("This offer has already been marked as done", 401);
        } else if ($backendstatus == $offer->PENDING) {
            return new JsonResponse("This offer was not accepted", 401);
        } else if ($backendstatus == $offer->FAILED) {
            return new JsonResponse("This offer has failed", 401);
        } else if ($backendstatus == $offer->REJECTED) {
            return new JsonResponse("This offer has been rejected", 401);
        } else {
            $offer->setStatus($status);
            $this->getDoctrine()->getManager()->persist($offer);
            $this->getDoctrine()->getManager()->flush();
            $response = new JsonResponse();
            $response->setStatusCode(201);
            $transaction = new Transaction();
            $transaction->setDate(new \DateTime('now'));
            $transaction->setOfferId($offer->getId());
            $transaction->setOffer($offer);
            $transaction->setDeleted(false);
            $transaction->setFinalPrice($offer->getRequestedPrice());
            $this->getDoctrine()->getManager()->persist($transaction);
            $this->getDoctrine()->getManager()->flush();
            $tangleId=$testrequest->getTangleId();
            $userTangleTable = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
                    $offerer = $userTangleTable->
                findOneBy(array('userId' => $offer->getUserId(), 'tangleId' => $tangleId));
            $offerer->setCredit($offerer->getCredit()+$transaction->getFinalPrice()); 
            $this->getDoctrine()->getManager()->persist($offerer);
            $this->getDoctrine()->getManager()->flush();
            $requeststatus=$testrequest->CLOSE;
            $testrequest->setStatus($requeststatus);
            $this->getDoctrine()->getManager()->persist($testrequest);
            $this->getDoctrine()->getManager()->flush();
            return $response;
  
        }
    }

}
