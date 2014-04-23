<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tag;

/**
 * RequestController takes the json Object
 * and fill the Request and Tag tables with data given
 * @author Salma Khaled
 */
class RequestController extends Controller {

    /**
     * this method is used to validate data and return response accordingly 
     * @param String $sessionId
     * @param \Megasoft\EntangleBundle\Entity\Session $session
     * @param Date $deadLineFormated
     * @param DateTime $dateFormated
     * @param int $requestedPrice
     * @param \Megasoft\EntangleBundle\Entity\Tangle $tangle
     * @param String $description
     * @param \Megasoft\EntangleBundle\Entity\User $user
     * @param String $date
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse|null
     * @author Salma Khaled
     */
    public function validate($sessionId, $session, $deadLineFormated, $dateFormated, $requestedPrice, $tangle, $description, $user, $date) {
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
        for ($i = 0; $i < $arrlength; $i++) {
            if ($user == $tangleUsers[$i]) {
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
        return null;
    }

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
        $sessionTable = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $tangleTable = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $userTable = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $session = $sessionTable->findOneBy(array('sessionId' => $sessionId));
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
        $user = $userTable->findOneBy(array('id' => $userId));
        $valid = $this->validate($sessionId, $session, $deadLineFormated, $dateFormated, $requestedPrice, $tangle, $description, $user, $date);
        if ($valid != null) {
            return $valid;
        }
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

}
