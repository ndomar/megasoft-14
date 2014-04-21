<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Megasoft\EntangleBundle\Entity\Offer;

/**
 * Description of CreateOfferController
 *
 * @author Salma Khaled
 */
class CreateOfferController extends Controller {

    /**
     * this method insert the data given from the sent json object to the offer table
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param String $tangleId
     * @param String $requestId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author Salma Khaled
     */
    public function createOfferAction(Request $request, $tangleId, $requestId) {
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
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
        if ($session == null) {
            $response->setStatusCode(401);
            return $response;
        }
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $userId = $session->getUserId();
        $user = $userTable->findOneBy(array('id' => $userId));
        $requestTable = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $theRequestId = (int) $requestId;
        $tangleRequest = $requestTable->findOneBy(array('id' => $theRequestId));
        if ($tangleRequest == null) {
            $response->setStatusCode(401);
            return $response;
        }
        if ($tangleRequest->getDeleted() || $tangleRequest->getStatus() == 2 || $tangleRequest->getStatus() == 3) {
            $response->setStatusCode(400);
            return $response;
        }
        $description = $json_array['description'];
        $date = $json_array['date'];
        $dateFormated = new \DateTime($date);
        $deadLine = $json_array['deadLine'];
        $deadLineFormated = new \DateTime($deadLine);
        $requestedPrice = $json_array['requestedPrice'];

        $newOffer = new Offer();
        $newOffer->setRequestedPrice($requestedPrice);
        $newOffer->setDate($dateFormated);
        $newOffer->setDescription($description);
        $newOffer->setExpectedDeadline($deadLineFormated);
        $newOffer->setUser($user);
        $newOffer->setRequest($tangleRequest);
        $newOffer->setStatus(1);
        //send notification
        $doctrine->getManager()->persist($newOffer);
        $doctrine->getManager()->flush();

        $response->setData(array('sessionId' => $sessionId));
        $response->setStatusCode(201);
        return $response;
    }

}
