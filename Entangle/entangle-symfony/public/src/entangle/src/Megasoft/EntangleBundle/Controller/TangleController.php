<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Megasoft\EntangleBundle\Entity\Session;

class TangleController extends Controller
{
    public function checkMembershipAction(Request $request,$tangleId)
    {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $jsonString = $request->getContent();
        $json = json_decode($jsonString,true);
        
        if( !isset($json['emails'] ) || $sessionId == null ){
            return new Response("Bad Request" , 400);
        }
        
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $tangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Tangle');
        $session = $sesionRepo->findOneBy(array('sessionId'=>$sessionId));
        
        if( $session == null ){
            return new Response("Unauthorized",401);
        }
        
        if( $tangleRepo->findOneById($tangleId) == null ){
            return new Response("Tangle Not Found",404);
        }
        
        $response = array();
        $response['notMembers'] = array();
        $response['entangleMembers'] = array();
        $response['alreadyInTheTangle'] = array();
        $response['invalid'] = array();
        
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');
        foreach($json['emails'] as $email){
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $response['invalid'][] = $email;
            }else{
                $mail =  $userEmailRepo->findOneByEmail($email);
                if( $mail == null ){
                    $response['notMembers'][] = $email;
                }else{
                    $userId = $mail->getUserId();
                    
                    if( $userTangleRepo->findOneBy(array('userId'=>$userId , 'tangleId'=>$tangleId)) == null){
                        $response['entangleMembers'][] = $email;
                    }else{
                        $response['alreadyInTheTangle'][] = $email;
                    }
                }
            }
        }
        
        $jsonResponse = new JsonResponse();
        $jsonResponse->setData($response);
        return $jsonResponse;
    }

    public function inviteAction($tangleId)
    {
    }

}
