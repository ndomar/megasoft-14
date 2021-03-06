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
class OfferController extends Controller
{

    /**
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $request
     * @param integer $sessionId
     * @return boolean true if the user can view this request and false otherwise
     * @author Almgohar
     */
    private function validateUser($request, $sessionId)
    {
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

    public function offerAction (\Symfony\Component\HttpFoundation\Request $req, $offerId) {
        $sessionId = $req->headers->get('X-SESSION-ID');

        if ($sessionId == null) {
            return new Response('Unauthorized', 401);
        }

        $doctrine = $this->getDoctrine();
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
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
     * @param \Megasoft\EntangleBundle\Entity\Message $messageTable
     * @param int $offerId
     * @return array $comments
     * @author Almgohar
     */
    private function getComments($messageTable, $offerId)
    {
        $comments = array();
        $messages = $messageTable->findBy(array('offerId' => $offerId));

        for ($i = 0; $i < count($messages); $i++) {
            $message = $messages[$i];

            if ($message == null || $message->getDeleted()) {
                continue;
            }

            if ($message->getSender()->getPhoto() == null) {
                $photo = null;
            } else {
                $filepath = 'http://'.$_SERVER['HTTP_HOST'].'/images/profilePictures/';
                $photo = $filepath.$message->getSender()->getPhoto();
            }

            $commenter = $message->getSender()->getName();
            $commentDate = $message->getDate()->format('d/m/Y');
            $comment = $message->getBody();
            $comments[] = array('commenterAvatar' => $photo, 'commenter' => $commenter,
                'comment' => $comment, 'commentDate' => $commentDate,);
        }

        return $comments;
    }

    /**
     *  Gets the information of the offer
     * @param \Megasoft\EntangleBundle\Entity\Offer $offer
     * @return array $offerInformation
     * @author Almgohar
     */
    private function getOfferInformation($offer)
    {
        $user = $offer->getUser();
        $offererId = $user->getId();
        $requesterId = $offer->getRequest()->getUserId();
        $requestId = $offer->getRequestId();
        $userName = $user->getName();
        $offerDate = $offer->getDate()->format('d/m/Y');

        if ($user->getPhoto() == null) {
            $photo = null;
        } else {
            $filepath = 'http://'.$_SERVER['HTTP_HOST'].'/images/profilePictures/';
            $photo = $filepath.$user->getPhoto();
        }

        $offerStatus = $offer->getStatus();
        $offerPrice = $offer->getRequestedPrice();
        $offerDescription = $offer->getDescription();
        $offerDeadline = $offer->getExpectedDeadline()->format('d/m/Y');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $request = $sesionRepo->findOneBy(array('id' => $requestId));
        $requestStatus = $request->getStatus();
        $offerInformation = array('offererAvatar' => $photo, 'offererName' => $userName,
            'offerDescription' => $offerDescription,
            'offerDeadline' => $offerDeadline,
            'offerStatus' => $offerStatus,
            'requesterId' => $requesterId,
            'offerPrice' => $offerPrice,
            'offererId' => $offererId,
            'offerDate' => $offerDate,
            'requestId' => $requestId,
            'requestStatus' => $requestStatus,
        );

        return $offerInformation;
    }

    /**
     * Changes the price of an offer.
     * @param Request $request
     * @param type $offerId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Mansour
     */

    public function changeOfferPriceAction(Request $request, $offerId) {

        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response("Null Session Header", 400);
        }
        $sessionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            return new Response("Unauthorized", 401);
        }
        $sessionExpired = $session->getExpired();
        if ($sessionExpired) {
            return new Response("Session expired", 440);
        }
        $offerRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer');
        $requestOffer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($requestOffer == null) {
            return new Response("Offer Not found", 404);
        }
        $oldPrice = $requestOffer->getRequestedPrice();
        if (($session->getUserId()) != ($requestOffer->getUserId())) {
            return new Response("Unauthorized", 401);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->ACCEPTED)) {
            return new Response("Offer is already accepted", 403);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->DONE)) {
            return new Response("Offer is already done", 403);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->FAILED)) {
            return new Response("Offer is already failed", 403);
        }
        if (($requestOffer->getStatus()) == ($requestOffer->REJECTED)) {
            return new Response("Offer is already rejected", 403);
        }
        if($requestOffer->getDeleted()){
            return new Response("Bad Request", 400);
        }
        $json = $request->getContent();
        $json_array = json_decode($json, true);
        if (!isset($json_array['newPrice'])){
            return new Response("Bad Request", 400);
        }
        $newOfferPrice = $json_array['newPrice'];
        if (\is_null($newOfferPrice)) {
            return new Response("Null New Price", 400);
        }
        if(!is_numeric($newOfferPrice)){
            return new Response("Non-Numeric New Price", 400);
        }
        if (($requestOffer->getRequestedPrice()) == $newOfferPrice) {
            return new Response("Same price, enter a new one", 409);
        }
        $requestOffer->setRequestedPrice($newOfferPrice);
        $notificationCenter = $this->get('notification_center.service');
        $notificationCenter->offerChangeNotification($requestOffer->getId(), $oldPrice);
        $this->getDoctrine()->getManager()->persist($requestOffer);
        $this->getDoctrine()->getManager()->flush();

        return new Response('The price of your offer has changed', 200);
    }

    /**
     * this recieves a request and calls verify to check if it can accept the offer
     * @param Request $request
     * @return Response $response returns 201 or 409 status code and message depending on verification
     * @author sak9
     */
    public function acceptOfferAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return $response = new Response("No Session Id.", 400);
        }
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            return $response = new Response("Error: Incorrect Session Id.", 400);
        }
        if ($session->getExpired() == 1) {
            return $response = new Response("Error: Session Expired.", 401);
        }
        $userOfSession = $session->getUserId();
        $json_array = json_decode($json, true);
        $offerId = $json_array['offerId'];
        if ($offerId == null) {
            return $response = new Response("Error: No offer selected.", 400);
        }
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($offer == null) {
            return $response = new Response("Error: No such offer.", 404);
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId,));
        $requesterId = $request->getUserId();
        $tangle = $request->getTangleId();
        if ($requesterId != $userOfSession) {
            return $response = new Response("Error: You are unauthorized to accept this offer.", 409);
        }
        $verificationMessage = $this->verify($offerId);
        if ($verificationMessage == "Offer Accepted.") {
            $response = new Response($verificationMessage, 201);
        } else {
            if ($verificationMessage == "Error: Not enough balance.") {
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
    public function verify($offerId)
    {
        $doctrine = $this->getDoctrine();
        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId,));
        if (count($offer) <= 0) {
            return "Error: No such offer.";
        }
        $requestId = $offer->getRequestId();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId,));
        $requesterId = $request->getUserId();
        $tangleId = $request->getTangleId();
        $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $requester = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $requesterId,));
        if (count($requester) <= 0) {
            return "Error: You don't belong to this tangle.";
        }
        if ($request->getDeleted() == 1) {
            return "Error: Request deleted.";
        }
        if ($request->getStatus() == $request->CLOSE) {
            return "Error: Request Closed.";
        }
        if ($request->getStatus() == $request->FROZEN) {
            return "Error: Request is Frozen.";
        }
        if ($offer->getDeleted() == 1) {
            return "Error: Offer deleted.";
        }
        if ($offer->getStatus() == $offer->DONE || $offer->getStatus() == $offer->ACCEPTED) {
            return "Error: Offer has already been accepted.";
        }
        if ($offer->getStatus() == $offer->FAILED || $offer->getStatus() == $offer->REJECTED) {
            return "Error: Offer closed.";
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
        $notificationCenter->offerChosenNotification($offerId);

        return "Offer Accepted.";
    }

    /**
     * An endpoint to withdraw an offer.
     * @param Request $request
     * @param integer $offerId
     * @return Response
     * @author OmarElAzazy
     */
    public function withdrawAction(Request $request, $offerId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($offerId == null || $sessionId == null) {
            return new Response('Bad Request', 400);
        }

        $doctrine = $this->getDoctrine();

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }

        $offererId = $session->getUserId();

        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->findOneBy(array('id' => $offerId));
        if ($offer == null || $offer->getUserId() != $offererId || $offer->getDeleted()) {
            return new Response('Unauthorized', 401);
        }

        if ($offer->getStatus() == $offer->ACCEPTED) {
            $this->unfreezePoints($offer->getRequest(), $offer->getRequestedPrice());
        }

        // notification
        $notificationCenter = $this->get('notification_center.service');
        $notificationCenter->offerDeletedNotification($offer->getId());


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
    public function unfreezePoints($request, $points)
    {
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
    private function verifyUser($request, $offerId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');

        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);

        if ($offerId == null || $sessionId == null || $json['body'] == null) {
            return new Response('Bad Request', 400);
        }

        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');

        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }

        $offerRepo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offer = $offerRepo->find($offerId);

        $user = $session->getUser();

        $userId = $user->getId();
        $tangleId = $offer->getRequest()->getTangleId();

        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'userId' => $userId));


        if ($userTangle == null) {
            return new Response('Unauthorized', 401);
        }

        return null;
    }

    /**
     * The endpoint responsible for adding comments on offers
     * @param Request $request
     * @param $offerId
     * @return null|Response
     */
    public function commentAction(Request $request, $offerId)
    {
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

        $notificationCenter = $this->get('notification_center.service');
        $notificationCenter->newMessageNotification($message->getId());

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
            return new Response('Unauthorized', 401);
        }
        $doctrine = $this->getDoctrine();
        $requestTable = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $offerId = $offerid;
        $offer = $repo->find($offerId);
        if ($offer == null) {
            return new Response('Offer does not exist', 404);
        }
        if ($offer->getDeleted()) {
            return new Response("Offer has been deleted", 404);
        }
        $requestid = $offer->getRequestId();
        $testrequest = $requestTable->find($requestid);
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Unauthorized', 401);
        }
        $userOfSession = $session->getUserId();
        if ($testrequest->getDeleted()) {
            return new Response('This request does not exist anymore', 404);
        }
        if ($testrequest->getStatus() == $testrequest->CLOSE) {
            return new Response('Request is closed', 400);
        }
        $status = $offer->DONE;
        $request = $offer->getRequest();
        $requesterId = $request->getUserId();
        if ($requesterId != $userOfSession) {
            return new Response("Error: You are unauthorized to mark this offer as done.", 401);
        }
        $backendstatus = $offer->getStatus();
        if ($backendstatus == $offer->DONE) {
            return new JsonResponse("Offer already marked as done", 400);
        } else if ($backendstatus == $offer->PENDING) {
            return new JsonResponse("Offer is not accepted", 400);
        } else if ($backendstatus == $offer->FAILED) {
            return new JsonResponse("This offer has failed", 400);
        } else if ($backendstatus == $offer->REJECTED) {
            return new JsonResponse("This offer is rejected", 400);
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
            $tangleId = $testrequest->getTangleId();
            $userTangleTable = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
            $offerer = $userTangleTable->
                    findOneBy(array('userId' => $offer->getUserId(), 'tangleId' => $tangleId));
            $offerer->setCredit($offerer->getCredit() + $transaction->getFinalPrice());

            $this->getDoctrine()->getManager()->persist($offerer);
            $this->getDoctrine()->getManager()->flush();
            $requeststatus = $testrequest->CLOSE;
            $testrequest->setStatus($requeststatus);
            $this->getDoctrine()->getManager()->persist($testrequest);
            $this->getDoctrine()->getManager()->flush();
            return $response;
        }
    }

    /**
     * this method insert the data given from the sent json object to the offer table
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param String $tangleId
     * @param String $requestId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Salma Khaled
     */
    public function createOfferAction(Request $request, $tangleId, $requestId)
    {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $response = new Response();
        $json_array = json_decode($json, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            $response->setStatusCode(400);
            $response->setContent("bad request");
            return $response;
        }
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired() == true) {
            $response->setStatusCode(401);
            $response->setContent("Unauthorized");
            return $response;
        }
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $userId = $session->getUserId();
        $user = $userTable->findOneBy(array('id' => $userId));
        $requestTable = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $offerTable = $doctrine->getRepository('MegasoftEntangleBundle:Offer');
        $theRequestId = (int)$requestId;
        $tangleRequest = $requestTable->findOneBy(array('id' => $theRequestId));
        $tangleTable = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $theTangleId = (int)$tangleId;
        $tangle = $tangleTable->findOneBy(array('id' => $theTangleId));
        $previousOffer = $offerTable->findOneBy(array('userId' => $userId, 'requestId' => $theRequestId));
        if ($previousOffer != null) {
            $response->setStatusCode(401);
            $response->setContent("Unauthorized");
            return $response;
        }
        $description = $json_array['description'];
        $date = $json_array['date'];
        $dateFormated = new \DateTime($date);
        $deadLine = $json_array['deadLine'];
        $deadLineFormated = new \DateTime($deadLine);
        $requestedPrice = $json_array['requestedPrice'];
        $valid = $this->validate($theRequestId, $tangle, $sessionId, $session, $deadLineFormated, $dateFormated, $requestedPrice, $tangleRequest, $description, $user, $date);
        if ($valid != null) {
            return $valid;
        }
        $newOffer = new Offer();
        $newOffer->setRequestedPrice($requestedPrice);
        $newOffer->setDate($dateFormated);
        $newOffer->setDescription($description);
        $newOffer->setExpectedDeadline($deadLineFormated);
        $newOffer->setUser($user);
        $newOffer->setRequest($tangleRequest);
        $newOffer->setStatus(0);


        $doctrine->getManager()->persist($newOffer);
        $doctrine->getManager()->flush();
        $response->setStatusCode(201);

        $notificationCenter = $this->get('notification_center.service');
        $notificationCenter->newOfferNotification($newOffer->getId());

        return $response;
    }

    /**
     * this method is used to validate data and return response accordingly
     * @param int $theRequestId
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @param String $sessionId
     * @param \Megasoft\EntangleBundle\Entity\Session $session
     * @param Date $deadLineFormated
     * @param DateTime $dateFormated
     * @param int $requestedPrice
     * @param \Megasoft\EntangleBundle\Entity\Request $tangleRequest
     * @param String $description
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @param String $date
     * @return \Symfony\Component\HttpFoundation\JsonResponse|null
     * @author Salma Khaled
     */
    public function validate($theRequestId, $tangle, $sessionId, $session, $deadLineFormated, $dateFormated, $requestedPrice, $tangleRequest, $description, $user, $date)
    {
        $response = new JsonResponse();
        if ($sessionId == null) {
            $response->setStatusCode(400);
            return $response;
        }
        if ($session == null || $session->getExpired() == true) {
            $response->setStatusCode(401);
            return $response;
        }
        if ($tangleRequest == null) {
            $response->setStatusCode(400);
            $response->setContent("no such request");
            return $response;
        }

        if ($tangle == null || $user == null) {
            $response->setStatusCode(401);
            return $response;
        }
        if ($tangle->getDeleted() == true) {
            $response->setStatusCode(401);
            $response->setContent("tangle is deleted");
            return $response;
        }
        $tangleUsers = $tangle->getUsers();
        $arrlength = count($tangleUsers);
        $userIsMember = false;
        $userId = $user->getId();
        for ($i = 0; $i < $arrlength; $i++) {
            if ($userId == $tangleUsers[$i]->getId()) {
                $userIsMember = true;
                break;
            }
        }
        if (!$userIsMember) {
            $response->setStatusCode(401);
            $response->setContent("User is not a member in the tangle");
            return $response;
        }
        $tangleRequests = $tangle->getRequests();
        $tangleRequetslength = count($tangleRequests);
        $requestBelongToTangle = false;
        for ($i = 0; $i < $tangleRequetslength; $i++) {
            if ($theRequestId == $tangleRequests[$i]->getId()) {
                $requestBelongToTangle = true;
                break;
            }
        }
        if (!$requestBelongToTangle) {
            $response->setStatusCode(401);
            $response->setContent("Request doesn't belong to tangle");
            return $response;
        }
        if ($tangleRequest->getDeleted()) {
            $response->setStatusCode(400);
            $response->setContent("request is deleted");
            return $response;
        }
        if ($tangleRequest->getStatus() == $tangleRequest->CLOSE || $tangleRequest->getStatus() == $tangleRequest->FROZEN) {
            $response->setStatusCode(400);
            $response->setContent("can not create offer on this request");
            return $response;
        }
        if ($tangleRequest->getUserId() == $userId) {
            $response->setStatusCode(400);
            $response->setContent("can not create offer on your request");
            return $response;
        }

        if ($description == null || $date == null || $requestedPrice == null) {
            $response->setStatusCode(400);
            $response->setContent("some data are missing");
            return $response;
        }
        if ($deadLineFormated->format("Y-m-d") < $dateFormated->format("Y-m-d")) {
            $response->setStatusCode(400);
            $response->setContent("deadline has passed!");
            return $response;
        }
        if ($requestedPrice < 0) {
            $response->setStatusCode(400);
            $response->setContent("price must be a positive value!");
            return $response;
        }

        return null;
    }

}
