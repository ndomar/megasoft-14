<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class createTangleController extends Controller
{
    public function createTangleAction(Request $request)
    {
        $json = $request->getContent();
        $json_array = json_decode($json,true);
        $tangleName = $json_array['tangleName'];
        $tangleIcon = $json_array['Image'];
                
        $Tangle = new Tangle();
        $Tangle->setName($tangleName);
        $Tangle->setIcon($tangleIcon);
        
        $this->getDoctrine()->getManager()->persist($Tangle);
        $this->getDoctrine()->getManager()->flush();
        
        $response = new Response();
        $response->setStatusCode(201);
        return $response;
    }
    
    public function checkAvailabilityAction($tangleName)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $repo->findOneBy(array('tangleName'=>$tangleName));
        
        if($tangle == null){
            return new Response('Not Found',404);
        }else{
            return new Response('Found',302);
        }
    }

}
