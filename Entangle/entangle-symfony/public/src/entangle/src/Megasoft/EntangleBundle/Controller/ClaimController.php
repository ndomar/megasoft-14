<?php

namespace Megasoft\EntangleBundle\Controller;

use DateTime;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class ClaimController extends Controller
{
    
     public function getSenderMail($sessionId)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        
        $user = $repo->findOneBy($sessionId);
        
        if($user == null){
            return new Response('Bad Request',400);
        }
        else{
            $usermail = $user->getEmail();
            $response = new JsonResponse();
            $response->setData($usermail);
            $response->setStatusCode(200);
            return $response;
        }
}
public function getRecieverMail($tangleId)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        
        $tangle = $repo->findOneBy($tangleId);
        
        if($tangle == null){
            return new Response('Bad Request',400);
        }
        else{
            $tangleOwnerMail = $tangle->getTangleOwner()->getEmail();
            $response = new JsonResponse();
            $response->setData($tangleOwnerMail);
            $response->setStatusCode(200);
            return $response;
        }
}

public function createClaim(Request $request)
    {
    
        $json = $request->getContent();
        $json_array = json_decode($json,true);
        $tangleId = $json_array['tangleId'];
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        
        $user = $repo->findOneBy($sessionId);
        
        $claim = new \Megasoft\EntangleBundle\Entity\Claim();
        $claim->setTangleId($tangleId);
        $claim->setUser($user);
        
        $claimId = $claim.getId();
        
        $this->getDoctrine()->getManager()->persist($claim);
        $this->getDoctrine()->getManager()->flush();
        
        $response = new JsonResponse();
        $response->setData(array('X-CLAIM-ID'=>$claimId));
        
        $response->setStatusCode(200);
        return $response;
    }
}