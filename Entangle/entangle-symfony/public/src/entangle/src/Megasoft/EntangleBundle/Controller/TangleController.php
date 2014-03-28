<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\InvitationCode;

class TangleController extends Controller
{
    private function validateTangleId($tangleId){
        $tangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Tangle');
        if( $tangleRepo->findOneById($tangleId) == null ){
            return false;
        }else{
            return true;
        }
    }
    
    private function isValidEmail($email){
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }
    
    private function isNewMember($email){
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $mail =  $userEmailRepo->findOneByEmail($email);
        return ($mail == null);
    }
    
    private function isTangleMember($email,$tangleId){
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');
        $mail =  $userEmailRepo->findOneByEmail($email);
        $userId = $mail->getUserId();
        return ($userTangleRepo->findOneBy(array('userId'=>$userId , 'tangleId'=>$tangleId)) != null);
                
    }
    
    public function checkMembershipAction(Request $request,$tangleId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if( $sessionId == null ){
            return new Response("Bad Request" , 400);
        }
        
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        
        $session = $sesionRepo->findOneBy(array('sessionId'=>$sessionId));
        
        if( $session == null ){
            return new Response("Unauthorized",401);
        }
        
        if( !$this->validateTangleId($tangleId) ){
            return new Response("Tangle Not Found",404);
        }
        

        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');
        
        if( $userTangleRepo->findOneBy(array('userId'=>$session->getUserId() , 'tangleId'=>$tangleId)) == null ){
            return new Response("You are not a tangle member to invite other members",401);
        }

        $jsonString = $request->getContent();
        $json = json_decode($jsonString,true); 
        
        if(!isset($json['emails'])){
            return new Response("Bad Request" , 400);
        }
        
        $response = array();
        $response['notMembers'] = array();
        $response['entangleMembers'] = array();
        $response['alreadyInTheTangle'] = array();
        $response['invalid'] = array();
        
        
        
        foreach($json['emails'] as $email){
            if(!$this->isValidEmail($email)){
                $response['invalid'][] = $email;
            }else if( $this->isNewMember($email)){
                $response['notMembers'][] = $email;
            }else if( $this->isTangleMember($email, $tangleId) ){
                $response['alreadyInTheTangle'][] = $email;
            }else{
                $response['entangleMembers'][] = $email;
            }
        }
        
        $jsonResponse = new JsonResponse();
        $jsonResponse->setData($response);
        return $jsonResponse;
    }

    public function inviteAction(Request $request, $tangleId)
    {
        
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if( $sessionId == null ){
            return new Response("Bad Request" , 400);
        }
        
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        
        $session = $sesionRepo->findOneBy(array('sessionId'=>$sessionId));
        
        if( $session == null ){
            return new Response("Unauthorized",401);
        }
        
        if( !$this->validateTangleId($tangleId) ){
            return new Response("Tangle Not Found",404);
        }
        
        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');
        
        if( ($userTangle = $userTangleRepo->findOneBy(array('userId'=>$session->getUserId() , 'tangleId'=>$tangleId))) == null ){
            return new Response("You are not a tangle member to invite other members",401);
        }
        
        $jsonString = $request->getContent();
        $json = json_decode($jsonString,true);
        
        if(!isset($json['emails']) || !isset($json['message']) ){
            return new Response("Bad Request" , 400);
        }
        
        $isOwner = $userTangle->getTangleOwner();
        
        foreach($json['emails'] as $email){
            
            if(!$this->isValidEmail($email) || $this->isTangleMember($email, $tangleId) ){
                continue;
            }
            
            if($isOwner){
                
                $randomString = $this->generateRandomString(30);

                $newInvitationCode = new InvitationCode();
                $newInvitationCode->setCode($randomString);
                if($this->isNewMember($email)){
                    $newInvitationCode->setUserId(null);
                }else{
                    $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
                    $userId =  $userEmailRepo->findOneByEmail($email)->getUserId();
                    $newInvitationCode->setNewMember($userId);
                }
                
                $newInvitationCode->setInviterId($session->getUserId());
                $newInvitationCode->setExpired(false);
                $newInvitationCode->setCreated(new DateTime("NOW"));
                
                $this->getDoctrine()->getManager()->persist($newInvitationCode);
                $this->getDoctrine()->getManager()->flush();
                
                $message = 'Hi , '.$json['message'].' , to accept the request'
                        . ' open this link http://entangle.io/invitation/'
                        . $randomString.' Best Regards .. BLA BLA BLA';
               
                // Mailer::sendEmail($email , $message ); // TO BE IMPLEMENTED
                
            
            }else{
                // TODO not this userstory
            }
        }
        
        
        
    }
    
    private function generateRandomString($len){
        $ret = '';
        $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        for($i=0;$i<$len;$i++){
            $ret .= $seed[rand(0,strlen($seed)-1)];
        }
        return $ret;
    }

}
