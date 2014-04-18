<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\InvitationCode;

class TangleController extends Controller {

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
     * This function is used to send the invitation mail to $email with the message $message and
     * creates the invitation code and send it to the user
     * @param string $email
     * @param integer $inviterId
     * @param string $message
     * @author MohamedBassem
     */
    public function inviteUser($email,$inviterId,$message){
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

        $newInvitationCode->setInviterId($inviterId);
        $newInvitationCode->setExpired(false);
        $newInvitationCode->setCreated(new \DateTime("NOW"));
        $newInvitationCode->setEmail($email);

        $this->getDoctrine()->getManager()->persist($newInvitationCode);
        $this->getDoctrine()->getManager()->flush();

        $message = 'Hi , ' . $message . ' , to accept the request'
                . ' open this link http://entangle.io/invitation/'
                . $randomString . ' Best Regards .. BLA BLA BLA';

        // Mailer::sendEmail($email , $message ); // TO BE IMPLEMENTED
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
                $this->inviteuser($email,$session->getUserId(),$json['message']);
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
     * Validates whether the user with the session id $sessionId is the owner of the tangle with
     * tangle id $tangleId , If yes the function returns null, returns the appropriate exception otherwise 
     * @param integer $sessionId
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @author MohamedBassem
     */
    public function validateIsOwner($sessionId,$tangleId){
        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }

        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');

        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null) {
            return new Response("Unauthorized", 401);
        }
        
        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        if (($userTangle = $userTangleRepo->findOneBy(array('userId' => $session->getUserId(), 'tangleId' => $tangleId))) == null || !$userTangle->getTangleOwner() ) {
            return new Response("Unauthorized", 401);
        }
        
        return null;
    }
    
    /**
     * The endpoint responsable for fetching the pending invitations for the tangle with id $tangleId
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @author MohamedBassem
     */
    public function pendingInvitationsAction(Request $request,$tangleId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        $validation = $this->validateIsOwner($sessionId,$tangleId);
        if($validation != null){
            return $validation;
        }
        
        $pendingInvitationTable = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PendingInvitation');
        $pendingInvitations = $pendingInvitationTable->findBy(array('tangleId'=>$tangleId,'approved'=>false));
        $responseArray = array();
        foreach($pendingInvitations as $pendingInvitation){
            $pending = array();
            $pending['id'] = $pendingInvitation->getId();
            if($pendingInvitation->getInvitee() == null){
                $pending['invitee'] = null;
            }else{
                $pending['invitee'] = $pendingInvitation->getInvitee()->getName();
            }
            $pending['inviter'] = $pendingInvitation->getInviter()->getName();
            $pending['email'] = $pendingInvitation->getEmail();
            $responseArray[] = $pending;
        }
        
        $jsonResponse = new JsonResponse();
        $jsonResponse->setData(array('pending-invitations'=>$responseArray));
        return $jsonResponse;
    }
    
    /**
     * An endpoint to accept the pending invitation with id $pendingInvitationId and sends the
     * invitation email to the user
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $pendingInvitationId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author MohamedBassem
     */
    public function acceptPendingInvitationAction(Request $request,$pendingInvitationId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        $pendingInvitationTable = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PendingInvitation');
        $pendingInvitation = $pendingInvitationTable->findOneBy(array('id'=>$pendingInvitationId));
        
        if($pendingInvitation == null){
            return new Response("Pending Invitation Not Found", 404);
        }
        
        if($pendingInvitation->getApproved()){
            return new Response("Bad Request", 400);
        }
            
        $validation = $this->validateIsOwner($sessionId,$pendingInvitation->getTangleId());
        
        if($validation != null){
            return $validation;
        }
        
        
        $message = $pendingInvitation->getMessage()->getBody();
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        $this->inviteuser($pendingInvitation->getEmail(),$session->getUserId(),$message);
        $pendingInvitation->setApproved(true);
        $this->getDoctrine()->getManager()->flush();
        return new Response("Approved",200);
    }
    
    /**
     * An endpoint to reject the pending invitation with id $pendingInvitationId
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $pendingInvitationId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author MohamedBassem
     */
    public function rejectPendingInvitationAction(Request $request,$pendingInvitationId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        $pendingInvitationTable = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PendingInvitation');
        $pendingInvitation = $pendingInvitationTable->findOneBy(array('id'=>$pendingInvitationId));
        
        if($pendingInvitation == null){
            return new Response("Pending Invitation Not Found", 404);
        }
        
        if($pendingInvitation->getApproved()){
            return new Response("Bad Request", 400);
        }
            
        $validation = $this->validateIsOwner($sessionId,$pendingInvitation->getTangleId());
        
        if($validation != null){
            return $validation;
        }
        
        $this->getDoctrine()->getManager()->remove($pendingInvitation);
        $this->getDoctrine()->getManager()->flush();
        return new Response("Deleted",200);
    }

}
