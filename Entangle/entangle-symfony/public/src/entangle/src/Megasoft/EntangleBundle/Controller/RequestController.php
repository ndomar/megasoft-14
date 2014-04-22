<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tag;

/**
 * RequestController takes the json Object
 * and fill the Request and Tag tables with data given
 * @author Salma Khaled
 */
class RequestController extends Controller {

    /**
     * take the json Object from the request then decode it and seprate 
     * the data and enter it in the Request Table
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param String $tangleId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author Salma Khaled
     */
    public function createAction(\Symfony\Component\HttpFoundation\Request $request, $tangleId) {
        $doctrine = $this->getDoctrine();
        $json = $request->getContent();
        $response = new JsonResponse();
        $json_array = json_decode($json, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        if ($sessionId == null) {
            $response->setStatusCode(400);
            return $response;
        }
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $tangleTable = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            $response->setStatusCode(401);
            return $response;
        }
        $userId = $session->getUserId();
        $description = $json_array['description'];
        $tags = $json_array['tags'];
        $date = $json_array['date'];
        $dateFormated = new \DateTime($date);
        $deadLine = $json_array['deadLine'];
        $deadLineFormated = new \DateTime($deadLine);
        $requestedPrice = $json_array['requestedPrice'];

        $theTangleId = (int) $tangleId;
        $tangle = $tangleTable->findOneBy(array('id' => $theTangleId));
        if ($tangle == null) {
            $response->setStatusCode(401);
            return $response;
        }
        $user = $userTable->findOneBy(array('id' => $userId));
        $newRequest = new Request();

        $newRequest->setTangle($tangle);
        $newRequest->setDescription($description);
        $newRequest->setStatus(1);

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
    public function addTags($newRequest, $tags) {
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
     * this returns a response depending on the size of the array it recieved from getRequestDetails 
     * @param  Int $requestId  Request id
     * @return Response 
     * @author sak93
     */
    public function viewRequestAction($requestId) {
        $requestDetails = $this->getRequestDetails($requestId);
        if (count($requestDetails) == 0) {
            return new Response("No such request.", 404);
        } else {
            $response = new JsonResponse();
            $response->setData(array($requestDetails));
            $response->setStatusCode(200);
            return $response;
        }
    }

    /**
     * this method makes an array of all the request details
     * @param  Int $requestId  Request id
     * @return Array $requestDetails 
     * @author sak93
     */
    public function getRequestDetails($requestId) {
        $repository = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $request = $repository->findOneBy(array('id' => $requestId));
        $requestDetails = array();
        if (count($request) != 0) {
            $requester = $request->getUserId();
            $description = $request->getDescription();
            $status = $request->getStatus();
            $date = $request->getDate();
            $deadline = $request->getDeadline();
            $icon = $request->getIcon();
            $price = $request->getRequestedPrice();
            $tangle = $request->getTangleId();
            $tags = $request->getTags();
            $offers = $this->getOfferDetails($requestId);
            $requestDetails = array('requester' => $requester, 'description' => $description,
                'status' => $status, 'date' => $date, 'deadline' => $deadline, 'icon' => $icon,
                'price' => $price, 'tangle' => $tangle, 'tags' => $tags, 'offers' => $offers);
        }
        return $requestDetails;
    }

    /**
     * this returns an array of offers associated with the request
     * @param  Int $requestId  Request id
     * @return Array $offerArray
     * @author sak93
     */
    public function getOfferDetails($requestId) {
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
                $details = array('description' => $description, 'status' => $status,
                    'date' => $date, 'deadline' => $deadline, 'price' => $price);
                array_push($offerArray, $details);
            }
        }
        return $offerArray;
    }

}
