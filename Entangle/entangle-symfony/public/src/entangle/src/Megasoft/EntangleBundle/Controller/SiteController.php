<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    /**
     * The action that renders the homepage
     * @return response
     * @author MohamedBassem
     */
    public function indexAction()
    {
        return $this->render('MegasoftEntangleBundle:Site:index.html.twig');
    }

}
