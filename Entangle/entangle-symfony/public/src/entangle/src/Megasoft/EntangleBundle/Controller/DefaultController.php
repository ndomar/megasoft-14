<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
 

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MegasoftEntangleBundle:Default:index.html.twig', array('name' => $name));

        
       }
       
      
    
    }
