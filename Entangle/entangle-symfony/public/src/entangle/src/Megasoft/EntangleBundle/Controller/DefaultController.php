<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {

        return $this->render('MegasoftEntangleBundle:Default:index.html.twig', array('name' => $name));
    }


    /**
     * Dummy endpoints that echos whatever it receives
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author MohamedBassem
     */
    public function dummyAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $content = $request->getContent();

        $content = $content == null ? "" : $content;

        return new Response($content, 200);
    }

}
