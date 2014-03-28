<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RequestsController extends Controller {
 
       public function getRequests($requestid) {
            $doctrine = $this->getDoctrine();
             $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
           
             $requested = $repo->findOneBy(array('request'=>$requestid ));
             
             
             
      
             
             return $requested;
      
             }
       

       
       
       public function deleteAction (Request $request , Userquest $user )
               
    {        
            $doctrine = $this->getDoctrine();
            $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
           
            $temp = $request->getID();
            $userid = $user->getID();
            $required = $user->getRequests($temp);
            
            
            
            unset ($this->$required);
           
            $this->getDoctrine()->getManager()->persist($request);
            $this->getDoctrine()->getManager()->flush();
            
            $response = new JsonResponse();
            $response->setStatusCode(200);
           
            return $response;

           
    } 
        
    }  