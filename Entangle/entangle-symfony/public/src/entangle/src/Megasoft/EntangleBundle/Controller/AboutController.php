<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\InvitationMessage;
use Megasoft\EntangleBundle\Entity\PendingInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Exception\LogicException;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;

class AboutController extends Controller {

    public function indexAction($name) {
        return $this->render('MegasoftEntangleBundle:About:about.html.twig', array('name' => $name));
    }
**
* Renders the About.html.twig page
* @param null
* @return rendered page
* @author Eslam Maged
*/
    public function  aboutAction() {
        return $this->render('MegasoftEntangleBundle:About:about.html.twig');
    }


}
