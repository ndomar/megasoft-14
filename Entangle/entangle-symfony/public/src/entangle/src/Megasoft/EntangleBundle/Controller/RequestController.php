<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Tag;
use Megasoft\EntangleBundle\Entity\Tangle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Tests\Controller;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Serializer\Exception\Exception;
use Symfony\Component\Translation\Tests\String;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class RequestController extends Controller{
    
    /**
      * A function to save an icon and return the url to it
      * @param string $iconData
      * @param integer $requestId
      * @return string $url
      * @author OmarElAzazy
      */
    private function saveIcon($iconData, $requestId){
        $decodedIcon = base64_decode($iconData);
        $icon = imagecreatefromstring($decodedIcon);
        
        $iconFileName = 'request' . "$requestId" . '.png';
        
        
        $kernel = $this->get('kernel');
        $path = $kernel->getRootDir();
        
        return new Response($path);
        
        $outputFilePath = '/vagrant/public/src/entangle/web/bundles/megasoftentangle/images/icons/' . $iconFileName;
        
        
        imagepng($icon, $outputFilePath, 9);
        imagedestroy($icon);
        return 'http://10.11.12.13/entangle/web/bundles/megasoftentangle/images/icons/' . $iconFileName;
    }
    
    /**
      * An endpoint to set the icon of a request
      * @param Request $request
      * @param integer $requestId
      * @return Response | Symfony\Component\HttpFoundation\JsonResponse
      * @author OmarElAzazy 
     */
    public function postIconAction(Request $request, $requestId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if($requestId == null || $sessionId == null){
            return new Response('Bad Request', 400);
        }
        
        $doctrine = $this->getDoctrine();
        
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if($session == null || $session->getExpired()){
            return new Response('Bad Request', 400);
        }
        
        $jsonString = $request->getContent();
        
        if($jsonString == null){
            return new Response('Bad Request', 400);
        }
        
        $json = json_decode($jsonString, true);
        $iconData = $json['requestIcon'];
        
        if($iconData == null){
            return new Response('Bad Request', 400);
        }
        
        $requesterId = $session->getUserId();
        
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $request = $requestRepo->findOneBy(array('id' => $requestId));
        if($request == null || $request->getUserId() != $requesterId){
            return new Response('Unauthorized', 401);
        }
        
        try{
            return $iconUrl = $this->saveIcon($iconData, $requestId);
        }
        catch (Exception $e){
            return new Response('Internal Server Error', 500);
        }
        
        $request->setIcon($iconUrl);
        
        $this->getDoctrine()->getManager()->persist($request);
        $this->getDoctrine()->getManager()->flush();
        
        $response = new JsonResponse();
        $response->setData(array('iconUrl' => $iconUrl));
        return $response;
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

    /**
     * take the json Object from the request then decode it and seprate 
     * the data and enter it in the Request Table
     * @param Request $request
     * @param String $tangleId
     * @return JsonResponse
     * @author Salma Khaled
     */
    public function createAction(Request $request, $tangleId) {
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
        $deadLineFormated = new DateTime2($deadLine);
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