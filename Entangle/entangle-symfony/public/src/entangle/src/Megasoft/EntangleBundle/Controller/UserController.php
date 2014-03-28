<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    private $tangle;

    public function confirmAction ($user , $tangle)
    {
       $userid = $user->getID();
       $tangleid  = $tangle->getID();
       
       $this->$tangle->UsersCount()++;
       $this->$tangle->addUser($user);
       $this->$user->acceptTangleInvitation($tangle);
       
       
        $this->getDoctrine()->getManager()->persist($tangle);
        $this->getDoctrine()->getManager()->flush();
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        
        
        $response = new JsonResponse();
        $response->setStatusCode(200);
           
            return $response;
        
        
        
        
    }
     
    
    
    
    
    
    
    
    
    
    
    
}