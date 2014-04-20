<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\InvitationCode;

class TangleController extends Controller
{
    /**
      * Validates that the request has correct format, session Id is active and of a user and that the user is in the tangle
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $tangleId
      * @return \Symfony\Component\HttpFoundation\Response | null
      * @author OmarElAzazy
      */
    private function verifyUser($request, $tangleId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if($tangleId == null || $sessionId == null){
            return new Response('Bad Request', 400);
        }
        
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if($session == null || $session->getExpired()){
            return new Response('Bad Request', 400);
        }
        
        $user = $session->getUser();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'userId' => $user->getId()));
        
        if($userTangle == null){
            return new Response('Unauthorized', 401);
        }
        
        return null;
    }
    
    /**
      * An endpoint to filter requests of a specific tangle by requester, tag, prefix of requester's name or description
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $tangleId
      * @return \Symfony\Component\HttpFoundation\Response | Symfony\Component\HttpFoundation\JsonResponse
      * @author OmarElAzazy
      */
    public function filterRequestsAction(\Symfony\Component\HttpFoundation\Request $request, $tangleId)
    { 
        $verification = $this->verifyUser($request, $tangleId);
        
        if($verification != null){
            return $verification;
        }
        
        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        
        $query = $requestRepo->createQueryBuilder('request')
                ->where('request.tangleId = :tangleId')
                ->setParameter('tangleId', $tangleId)
                ->andWhere('request.deleted = :false')
                ->setParameter('false', false);
        
        $userId = $request->query->get('userid', null);
        if($userId != null){
            $query = $query->andWhere('request.userId = :userId')
                    ->setParameter('userId', $userId);
        }
        
        $fullText = $request->query->get('fulltext', null);
        if($fullText != null){
            $query = $query->andWhere('request.description LIKE :fullTextFormat')
                    ->setParameter('fullTextFormat', '%' . $fullText . '%');
        }
        
        
        $usernamePrefix = $request->query->get('usernameprefix', null);
        if($usernamePrefix != null){
            $query = $query->innerJoin('MegasoftEntangleBundle:User', 'user', 'WITH', 'request.userId = user.id')
                    ->andWhere('user.name LIKE :usernamePrefixFormat')
                    ->setParameter('usernamePrefixFormat', $usernamePrefix . '%');
        }
        
        $requests = $query->getQuery()->getResult();
        
        $tagId = $request->query->get('tagid', null);
        $requestsJsonArray = array();
        
        foreach($requests as $tangleRequest){
            
            if($tagId != null){
                $foundTag = false;
                foreach($tangleRequest->getTags() as $tag){
                    if($tag->getId() == $tagId){
                        $foundTag = true;
                        break;
                    }
                }
                
                if(!$foundTag){
                    continue;
                }
            }
            
            $requestsJsonArray[] = array(
                                        'id' => $tangleRequest->getId(),
                                        'username' => $tangleRequest->getUser()->getName(),
                                        'userId' => $tangleRequest->getUserId(),
                                        'description' => $tangleRequest->getDescription(),
                                        'offersCount' => sizeof($tangleRequest->getOffers())
                                    );
        }
        
        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($requestsJsonArray), 'requests' => $requestsJsonArray));
        
        return $response;
    }
    
    /**
      * An endpoint to return the list of tags in a specific tangle
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $tangleId
      * @return \Symfony\Component\HttpFoundation\Response | Symfony\Component\HttpFoundation\JsonResponse
      * @author OmarElAzazy
      */
    public function allTagsAction(\Symfony\Component\HttpFoundation\Request $request, $tangleId){
        $verification = $this->verifyUser($request, $tangleId);
        
        if($verification != null){
            return $verification;
        }
        
        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $criteria = array('tangleId' => $tangleId, 'deleted' => false);
        $requests = $requestRepo->findBy($criteria);
        
        $tags = array();
        foreach($requests as $tangleRequest){
            $tags = array_merge($tags, $tangleRequest->getTags()->toArray());
        }
        
        $tags = array_unique($tags);
        
        $tagsJsonArray = array();
        
        foreach($tags as $tag){
            $tagsJsonArray[] = array(
                                    'id' => $tag->getId(),
                                    'name' => $tag->getName()
                                );
        }
        
        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($tagsJsonArray), 'tags' => $tagsJsonArray));
        
        return $response;
    }
    

    /**
     * Validates the existance of a certain tangle
     * @param integer $tangleId
     * @return boolean true if the tangle exists , false otherwise
     * @author MohamedBassem
     */
    private function validateTangleId($tangleId) {
        $tangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Tangle');
        if ($tangleRepo->findOneById($tangleId) == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates that the email is a valid email
     * @param string $email
     * @return boolean true if the email is valid , false otherwise
     * @author MohamedBassem
     */
    private function isValidEmail($email) {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * checks if this email is an entangle member or not
     * @param string $email
     * @return boolean true if the member is new , false otherwise
     * @author MohamedBassem
     */
    private function isNewMember($email) {
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $mail = $userEmailRepo->findOneByEmail($email);
        return ($mail == null);
    }

    /**
     * Checks if a certain email belongs to a user that is in the tangle
     * @param integer $email
     * @param integer $tangleId
     * @return boolean true if the user belongs to the tangle , false otherwise
     * @author MohamedBassem
     */
    private function isTangleMember($email, $tangleId) {
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');
        $mail = $userEmailRepo->findOneByEmail($email);
        $userId = $mail->getUserId();
        return ($userTangleRepo->findOneBy(array('userId' => $userId, 'tangleId' => $tangleId)) != null);
    }

    /**
     * An endpoint that gets a list of emails and classify them to
     * newMember , Entangle Member not in the tangle , already in the tangle
     * and invalid emails
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author MohamedBassem
     */
    public function checkMembershipAction(Request $request, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }

        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');

        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null) {
            return new Response("Unauthorized", 401);
        }

        if (!$this->validateTangleId($tangleId)) {
            return new Response("Tangle Not Found", 404);
        }


        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        if ($userTangleRepo->findOneBy(array('userId' => $session->getUserId(), 'tangleId' => $tangleId)) == null) {
            return new Response("You are not a tangle member to invite other members", 401);
        }

        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);

        if (!isset($json['emails'])) {
            return new Response("Bad Request", 400);
        }

        $response = array();
        $response['notMembers'] = array();
        $response['entangleMembers'] = array();
        $response['alreadyInTheTangle'] = array();
        $response['invalid'] = array();



        foreach ($json['emails'] as $email) {
            if (!$this->isValidEmail($email)) {
                $response['invalid'][] = $email;
            } else if ($this->isNewMember($email)) {
                $response['notMembers'][] = $email;
            } else if ($this->isTangleMember($email, $tangleId)) {
                $response['alreadyInTheTangle'][] = $email;
            } else {
                $response['entangleMembers'][] = $email;
            }
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setData($response);
        return $jsonResponse;
    }

    /**
     * An endpoint to invite a list of emails to join a certain tangle
     * it creates the invitation code and send it to the user
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author MohamedBassem
     */
    public function inviteAction(Request $request, $tangleId) {

        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }

        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');

        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null) {
            return new Response("Unauthorized", 401);
        }

        if (!$this->validateTangleId($tangleId)) {
            return new Response("Tangle Not Found", 404);
        }

        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        if (($userTangle = $userTangleRepo->findOneBy(array('userId' => $session->getUserId(), 'tangleId' => $tangleId))) == null) {
            return new Response("You are not a tangle member to invite other members", 401);
        }

        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);

        if (!isset($json['emails']) || !isset($json['message'])) {
            return new Response("Bad Request", 400);
        }

        $isOwner = $userTangle->getTangleOwner();

        foreach ($json['emails'] as $email) {

            if (!$this->isValidEmail($email) || (!$this->isNewMember($email) && $this->isTangleMember($email, $tangleId) )) {
                continue;
            }

            if ($isOwner) {

                $randomString = $this->generateRandomString(30);

                $newInvitationCode = new InvitationCode();
                $newInvitationCode->setCode($randomString);
                if ($this->isNewMember($email)) {
                    $newInvitationCode->setUserId(null);
                } else {

                    $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
                    $user = $userEmailRepo->findOneByEmail($email)->getUser();
                    $newInvitationCode->setUser($user);
                }

                $newInvitationCode->setInviterId($session->getUserId());
                $newInvitationCode->setExpired(false);
                $newInvitationCode->setCreated(new \DateTime("NOW"));
                $newInvitationCode->setEmail($email);

                $this->getDoctrine()->getManager()->persist($newInvitationCode);
                $this->getDoctrine()->getManager()->flush();

                $message = 'Hi , ' . $json['message'] . ' , to accept the request'
                        . ' open this link http://entangle.io/invitation/'
                        . $randomString . ' Best Regards .. BLA BLA BLA';

                // Mailer::sendEmail($email , $message ); // TO BE IMPLEMENTED
            } else {
                // TODO not this userstory
            }
        }

        return new Response("Invitation Sent", 200);
    }

    /**
     * Generates a random string of length $len
     * @param integer $len
     * @return string a randomly generated string of length $len
     * @author MohamedBassem
     */
    private function generateRandomString($len) {
        $ret = '';
        $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        for ($i = 0; $i < $len; $i++) {
            $ret .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $ret;
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @author HebaAamer
     */
    private function leaveTangleVerification($request, $tangleId) {
        $verification = $this->verifyUser($request, $tangleId);
        
        if($verification != null){
            return $verification;
        }
                
        $sessionId = $request->headers->get("X-SESSION_ID");
        
        $doctrine = $this->getDoctrine();
        
        $sessionRepo = $doctrine->getRepository("MegasoftEntangleBundle:Session");
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $userId = $session->getUserId();
        
        $userTangleRepo = $doctrine->getRepository("MegasoftEntangleBundle:UserTangle");
        if (($userTangle = $userTangleRepo
                ->findOneBy(array('userId' => $userId, 
                    'tangleId' => $tangleId, 'tangleOwner' => true))) != null) {
            return new Response("Forbidden", 403);
        }
        
        return null;
    }
    
    /**
     * A function that is reponsible for deleting the requests 
     * of a user that left the tangle
     * 
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeRequests($tangleId, $userId) {
        
    }
    
    
    /**
     * A function that is responsible for deleting the offers 
     * of a user that left the tangle
     * 
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeOffers($tangleId, $userId) {
        
    }
    
    //remaining the deletion of userTangle or the updating of the left / leavingDate
    
    //it covers deleting the userTangle from the userTangles of the tangle
    //it covers updating the deletedBalance of the tangle
    /**
     * A function that is responsible for removing a user from 
     * a tangle and updating the deletedBalance of the tangle
     * 
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeUser($tangleId, $userId) {
        $doctrine = $this->getDoctrine();
        $userTangleRepo = $doctrine->getRepository("MegasoftEntangleBundle:UserTangle");
        
        $userTangle = $userTangleRepo->findOneBy(array('userId' => $userId, 'tangleId' => $tangleId));
        if($userTangle != null){
            $tangleRepo = $doctrine->getRepository("MegasoftEntangleBundle:Tangle");
            
            $tangle = $tangleRepo->find($tangleId);
            if($tangle != null) {
                $tangle->removeUserTangle($userTangle);
            
                $deletedBalance = $tangle->getDeletedBalance();
                $deletedBalance = $deletedBalance + $userTangle->getCredit();
            
                $tangle->setDeletedBalance($deletedBalance);
            }
        }
    }


    /**
     * An endpoint to be used when a user leaves a tangle
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @param integer $userId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author HebaAamer
     */
    public function leaveTangleAction(Request $request, $tangleId) {
        $verified = $this->leaveTangleVerification($request, $tangleId);
        if($verified != null){
            return $verified;
        }
        
        $sessionId = $request->headers->get("X-SESSION_ID");
        
        $doctrine = $this->getDoctrine();
        
        $sessionRepo = $doctrine->getRepository("MegasoftEntangleBundle:Session");
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $userId = $session->getUserId();
        
        $this->removeOffers($tangleId, $userId);
        $this->removeRequests($tangleId, $userId);
        $this->removeUser($tangleId, $userId);
        
    }

}
