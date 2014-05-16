<?php

namespace Megasoft\EntangleBundle\Controller;

use DateTime as DateTime2;
use Megasoft\EntangleBundle\Entity\Tag;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as Request2;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Session;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class RequestController extends Controller
{

    /**
     * A function to save an icon and return the url to it
     * @param string $iconData
     * @param integer $requestId
     * @return string $url
     * @author OmarElAzazy
     */
    private function saveIcon($iconData, $requestId)
    {
        $decodedIcon = base64_decode($iconData);
        $icon = imagecreatefromstring($decodedIcon);

        $iconFileName = 'request' . "$requestId" . '.png';
        $kernel = $this->get('kernel');
        $path = $kernel->getRootDir() . '/../web/bundles/megasoftentangle/images/request/icons/';

        $outputFilePath = $path . $iconFileName;
        imagepng($icon, $outputFilePath, 9);
        imagedestroy($icon);
        return 'http://10.11.12.13/entangle/web/bundles/megasoftentangle/images/request/icons/' . $iconFileName;
    }

    /**
     * An endpoint to set the icon of a request
     * @param Request $request
     * @param integer $requestId
     * @return Response | Symfony\Component\HttpFoundation\JsonResponse
     * @author OmarElAzazy
     */
    public function postIconAction(Request2 $request, $requestId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($requestId == null || $sessionId == null) {
            return new Response('Bad Request', 400);
        }

        $doctrine = $this->getDoctrine();

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }

        $jsonString = $request->getContent();

        if ($jsonString == null) {
            return new Response('Bad Request', 400);
        }

        $json = json_decode($jsonString, true);
        $iconData = $json['requestIcon'];

        if ($iconData == null) {
            return new Response('Bad Request', 400);
        }

        $requesterId = $session->getUserId();

        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        if ($request == null || $request->getUserId() != $requesterId) {
            return new Response('Unauthorized', 401);
        }

        try {
            $iconUrl = $this->saveIcon($iconData, $requestId);
        } catch (Exception $e) {
            return new Response('Internal Server Error', 500);
        }

        $request->setIcon($iconUrl);

        $this->getDoctrine()->getManager()->persist($request);
        $this->getDoctrine()->getManager()->flush();

        $response = new JsonResponse();
        $response->setData(array('iconUrl' => $iconUrl));
        return $response;
    }

    /* Reopens a closed request.
     * @param Request $request
     * @param int $requestId
     * @author Mansour
     */

    public function reOpenRequestAction(Request2 $request, $requestId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            return new Response("Invalid session header", 400);
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
        $requestRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $tangleRequest = $requestRepo->findOneBy(array('id' => $requestId));
        if ($tangleRequest == null) {
            return new Response("Not Found", 404);
        }
        if (($session->getUserId()) != ($tangleRequest->getUserId())) {
            return new Response("Unauthorized", 401);
        }
        if ($tangleRequest->getStatus() == $tangleRequest->OPEN) {
            return new Response("Request is already open", 400);
        }
        if ($tangleRequest->getStatus() == $tangleRequest->FROZEN) {
            return new Response("Request is frozen", 400);
        }
        if ($tangleRequest->getStatus() == $tangleRequest->CLOSE) {
            $tangleRequest->setStatus($tangleRequest->OPEN);
            $this->getDoctrine()->getManager()->persist($tangleRequest);
            $this->getDoctrine()->getManager()->flush();

            // notification
            $notificationCenter = $this->get('notification_center.service');
            $title = "request reopen";
            $body = "{{from}} reopened his request";
            $notificationCenter->reopenRequestNotification($tangleRequest->getId(), $title, $body);

            return new Response('Reopened', 200);
        }
    }

    /**
     * this returns a response depending on the size of the array it recieved from getRequestDetails
     * @param  Int $requestId Request id
     * @return Response
     * @author sak93
     */
    public function viewRequestAction($tangleId, $requestId, Request2 $request)
    {
        $doctrine = $this->getDoctrine();
        $sessionId = $request->headers->get('X-SESSION-ID');
        $response = new JsonResponse();
        if ($sessionId == null) {
            $response->setData(array('Error' => 'No session Id.'));
            $response->setStatusCode(400);
            return $response;
        }
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            $response->setData(array('Error' => 'Incorrect Session Id.'));
            $response->setStatusCode(400);
            return $response;
        }
        if ($session->getExpired() == 1) {
            $response->setData(array('Error' => 'Session Expired.'));
            $response->setStatusCode(400);
            return $response;
        }
        $sessionUserId = $session->getUserId();
        $userTangle = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $viewer = $userTangle->findOneBy(array('tangleId' => $tangleId, 'userId' => $sessionUserId));
        if (count($viewer) <= 0) {
            $response->setData(array('Error' => 'You do not belong to this tangle.'));
            $response->setStatusCode(400);
            return $response;
        }

        $requestDetails = $this->getRequestDetails($requestId, $sessionUserId, $tangleId);

        if (count($requestDetails) == 0) {
            $response->setData(array('Error' => 'No such request.'));
            $response->setStatusCode(400);
            return $response;
        }

        $response = new JsonResponse();
        $response->setData($requestDetails);
        $response->setStatusCode(200);
        $response->headers->set('X-SESSION-ID', $sessionId);
        return $response;
    }

    /**
     * this method makes an array of all the request details
     * @param  Int $requestId Request id
     * @return Array $requestDetails
     * @author sak93
     */
    public function getRequestDetails($requestId, $sessionUserId, $tangleId)
    {
        $repository = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $request = $repository->findOneBy(array('id' => $requestId));
        $requestDetails = array();
        if (count($request) != 0) {
            if ($request->getTangleId() != $tangleId) {
                return $requestDetails;
            }
            $requester = $request->getUserId();
            $description = $request->getDescription();
            $status = $request->getStatus();
            $date = $request->getDate();
            $deadline = $request->getDeadline();
            $icon = $request->getIcon();
            $price = $request->getRequestedPrice();
            $tangle = $request->getTangleId();
            $tempTags = $request->getTags();
            $tags = array();
            for ($i = 0; $i < count($tempTags); $i++) {
                $tags[] = $tempTags[$i]->getName();
            }
            $offers = $this->getOfferDetails($requestId);
            $myRequest = 0;
            if ($sessionUserId == $requester) {
                $myRequest = 1;
            }
            $requestDetails = array('requester' => $requester, 'requesterName'=>$request->getUser()->getName() ,'description' => $description,
                'status' => $status, 'MyRequest' => $myRequest, 'date' => $date, 'deadline' => $deadline, 'icon' => $icon,
                'price' => $price, 'tangle' => $tangle, 'tags' => $tags, 'offers' => $offers);
        }
        return $requestDetails;
    }

    /**
     * this returns an array of offers associated with the request
     * @param  Int $requestId Request id
     * @return Array $offerArray
     * @author sak93
     */
    public function getOfferDetails($requestId)
    {
        $repository = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Offer');
        $allOffers = $repository->findAll(array('id' => $requestId));
        $offerArray = array();
        $numOfOffers = count($allOffers);
        for ($i = 0; $i < $numOfOffers; $i++) {
            $offer = $repository->find($allOffers[$i]->getId());
            $request = $offer->getRequestId();
            if ($request == $requestId) {
                $description = $offer->getDescription();
                $status = $offer->getStatus();
                $date = $offer->getDate();
                $deadline = $offer->getExpectedDeadline();
                $price = $offer->getRequestedPrice();
                $deleted = $offer->getDeleted();
                $details = array('description' => $description, 'status' => $status,
                    'date' => $date, 'deadline' => $deadline, 'price' => $price, 'offererName' => $offer->getUser()->getName(), 'id' => $offer->getId());
                if ($deleted == 0) {
                    array_push($offerArray, $details);
                }
            }
        }
        return $offerArray;
    }

    /**
     * this method is used to validate data and return response accordingly
     * @param String $sessionId
     * @param Session $session
     * @param Date $deadLineFormated
     * @param DateTime $dateFormated
     * @param int $requestedPrice
     * @param Tangle $tangle
     * @param String $description
     * @param User $user
     * @param String $date
     * @return Response|JsonResponse|null
     * @author Salma Khaled
     */
    public function validate($sessionId, $session, $deadLineFormated, $dateFormated, $requestedPrice, $tangle, $description, $user, $date)
    {
        $response = new JsonResponse();
        if ($sessionId == null) {
            $response->setStatusCode(400);
            $response->setContent("bad request");
            return $response;
        }
        if ($session == null || $session->getExpired() == true) {
            $response->setStatusCode(401);
            $response->setContent("Unauthorized");
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
        if ($description == null || $date == null) {
            $response->setStatusCode(400);
            $response->setContent("some data are missing");
            return $response;
        }
        if ($deadLineFormated != null) {
            if ($deadLineFormated->format("Y-m-d") < $dateFormated->format("Y-m-d")) {
                $response->setStatusCode(400);
                $response->setContent($deadLineFormated->format("Y-m-d"));
                return $response;
            }
        }
        if ($requestedPrice < 0) {
            $response->setStatusCode(400);
            $response->setContent("price must be a positive value!");
            return $response;
        }
        return null;
    }

    /**
     * take the json Object from the request then decode it and seprate
     * the data and enter it in the Request Table
     * @param Request2 $request
     * @param String $tangleId
     * @return JsonResponse
     * @author Salma Khaled
     */
    public function createAction(Request2 $request, $tangleId)
    {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $response = new JsonResponse();
        $json_array = json_decode($json, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            $response->setStatusCode(400);
            $response->setContent("bad request");
            return $response;
        }

        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $tangleTable = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired() == true) {
            $response->setStatusCode(401);
            $response->setContent("Unauthorized");
            return $response;
        }
        $userId = $session->getUserId();
        $description = $json_array['description'];
        $tags = $json_array['tags'];
        $date = $json_array['date'];
        $dateFormated = new DateTime2($date);
        $deadLine = $json_array['deadLine'];
        if ($deadLine == "") {
            $deadLineFormated = null;
        } else {
            $deadLineFormated = new DateTime2($deadLine);
        }
        $requestedPrice = $json_array['requestedPrice'];
        if ($requestedPrice == "") {
            $requestedPrice = null;
        }
        $theTangleId = (int)$tangleId;
        $tangle = $tangleTable->findOneBy(array('id' => $theTangleId));
        $user = $userTable->findOneBy(array('id' => $userId));
        $valid = $this->validate($sessionId, $session, $deadLineFormated, $dateFormated, $requestedPrice, $tangle, $description, $user, $date);
        if ($valid != null) {
            return $valid;
        }
        $newRequest = new Request();
        $newRequest->setTangle($tangle);
        $newRequest->setDescription($description);
        $newRequest->setStatus(0);
        $newRequest->setDate($dateFormated);
        $newRequest->setDeadLine($deadLineFormated);
        $newRequest->setUser($user);
        $newRequest->setRequestedPrice($requestedPrice);
        $this->addTags($newRequest, $tags);
        $doctrine->getManager()->persist($newRequest);
        $doctrine->getManager()->flush();
        $response->setData(array('sessionId' => $sessionId));
        $response->setStatusCode(201);
        return $response;
    }

    /**
     * this function is responsible for filling the Tag Table it creates
     * a new Tag if the tag didn't exist before
     * it also add the tag to the created Request realated to it
     * @param Request $newRequest
     * @param json_array $tags
     * @author Salma Khaled
     */
    public function addTags($newRequest, $tags)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Tag');
        $arrlength = count($tags);
        for ($i = 0; $i < $arrlength; $i++) {
            $tag = $repo->findOneBy(array('name' => $tags[$i]));
            if ($tag == null) {
                $tag = new Tag();
                $tag->setName($tags[$i]);
            }
            $newRequest->addTag($tag);
            $doctrine->getManager()->persist($tag);
            $doctrine->getManager()->flush();
        }
    }

    /**
     * An endpoint to delete a request.
     * @param Request2 $request
     * @param integer $requestId
     * @return Response
     * @author OmarElAzazy
     */
    public function deleteAction(Request2 $request, $requestId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($requestId == null || $sessionId == null) {
            return new Response('Bad Request', 400);
        }

        $doctrine = $this->getDoctrine();

        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }

        $requesterId = $session->getUserId();

        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        if ($request == null || $request->getUserId() != $requesterId) {
            return new Response('Unauthorized', 401);
        }

        // notification
        $notificationCenter = $this->get('notification_center.service');
        $title = "request deleted";
        $body = "{{from}} deleted his request";
        $notificationCenter->requestDeletedNotification($request->getId(), $title, $body);

        $request->setDeleted(true);
        $request->setStatus($request->CLOSE);
        $this->getDoctrine()->getManager()->persist($request);
        $this->getDoctrine()->getManager()->flush();

        return new Response("Deleted", 204);
    }

}
