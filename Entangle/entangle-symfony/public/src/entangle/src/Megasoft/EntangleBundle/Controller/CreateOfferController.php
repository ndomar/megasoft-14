<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CreateOfferController responsible for creating offers
 *
 * @author Salma Khaled
 */
class CreateOfferController extends Controller
{

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
            $response->setContent("Please login again");
            return $response;
        }
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired() == true) {
            $response->setStatusCode(401);
            $response->setContent("Please login again");
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
            $response->setContent("You have already made an offer on this request");
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

//        send notification

        $notificationCenter = $this->get('notification_center.service');
        $title = "new offer";
        $body = "{{from}} made a new offer to your request";
        $notificationCenter->newOfferNotification($newOffer->getId(), $title, $body);

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
            $response->setContent("Please login again");
            $response->setStatusCode(400);
            return $response;
        }
        if ($session == null || $session->getExpired() == true) {
            $response->setContent("Please login again");
            $response->setStatusCode(401);
            return $response;
        }
        if ($tangleRequest == null) {
            $response->setStatusCode(400);
            $response->setContent("No such request");
            return $response;
        }

        if ($tangle == null || $user == null) {
            $response->setContent("Please choose a tangle, or user Id");
            $response->setStatusCode(401);
            return $response;
        }
        if ($tangle->getDeleted() == true) {
            $response->setStatusCode(401);
            $response->setContent("Tangle has been deleted");
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
            $response->setContent("User is not a member of this tangle");
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
            $response->setContent("This request doesn't belong to this tangle");
            return $response;
        }
        if ($tangleRequest->getDeleted()) {
            $response->setStatusCode(400);
            $response->setContent("This request has been deleted");
            return $response;
        }
        if ($tangleRequest->getStatus() == $tangleRequest->CLOSE || $tangleRequest->getStatus() == $tangleRequest->FROZEN) {
            $response->setStatusCode(400);
            $response->setContent("An offer has already been accepted for this request");
            return $response;
        }
        if ($tangleRequest->getUserId() == $userId) {
            $response->setStatusCode(400);
            $response->setContent("You can not create an offer on your own request");
            return $response;
        }

        if ($description == null || $date == null || $requestedPrice == null) {
            $response->setStatusCode(400);
            $response->setContent("Please enter all fields");
            return $response;
        }
        if ($deadLineFormated->format("Y-m-d") < $dateFormated->format("Y-m-d")) {
            $response->setStatusCode(400);
            $response->setContent("This deadline has passed, please enter a valid date");
            return $response;
        }
        if ($requestedPrice < 0) {
            $response->setStatusCode(400);
            $response->setContent("Please enter a postitive value");
            return $response;
        }

        return null;
    }

}
