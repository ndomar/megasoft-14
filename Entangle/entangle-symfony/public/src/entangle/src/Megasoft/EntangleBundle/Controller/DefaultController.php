<?php

namespace Megasoft\EntangleBundle\Controller;

use DateTime;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Message;
use Megasoft\EntangleBundle\Entity\NewMessageNotification;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\PriceChangeNotification;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tag;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Transaction;
use Megasoft\EntangleBundle\Entity\TransactionNotification;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {

        return $this->render('MegasoftEntangleBundle:Default:index.html.twig', array('name' => $name));
    }
}
