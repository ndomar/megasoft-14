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
use Symfony\Component\BrowserKit\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MegasoftEntangleBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function registerAction(Request $request) {
        if($request->getMethod() == 'POST') {
            
        }
        return $this->render('MegasoftEntangleBundle:Default:register.html.twig');
    }


//    public function testAction(){
//        $doctrineManger = $this->getDoctrine()->getManager();
//        
//        $user = new User();
//        
//        $userEmail = new UserEmail();
//        $userEmail->setEmail("test@megasoft.com");
//        
//        $user->setName("Mohamed");
//        $user->setPassword(md5("test"));
//        $user->setVerified(0); 
//        $user->addEmail($userEmail);
//        
//        $userTangle = new UserTangle();
//        $userTangle->setCredit(100);
//        $userTangle->setTangleOwner(0);
//        
//        $tangle = new Tangle();
//        $tangle->setName("Prince el koon");
//        $tangle->addUserTangle($userTangle);
//        
//        $user->addUserTangle($userTangle);
//        
//        $request = new Request();
//        $request->setTangle($tangle);
//        $request->setStatus(1);
//        $request->setDescription("test");
//        $request->setDate(new \DateTime("NOW"));
//        $request->setUser($user);
//        
//        $tag1 = new Tag();
//        $tag1->setName("prince");
//        $request->addTag($tag1);
//        
//        $tag2 = new Tag();
//        $tag2->setName("prince2");
//        //$tag2->addRequest($request); // Not Working yet
//        
//        $offer = new Offer();
//        $offer->setRequestedPrice(20);
//        $offer->setDate(new \DateTime());
//        $offer->setDescription("lkjadfhsf");
//        $offer->setStatus(0);
//        
//        $request->addOffer($offer);
//        
//        $offer2 = new Offer();
//        $offer2->setRequestedPrice(20);
//        $offer2->setDate(new DateTime());
//        $offer2->setDescription("lkjadfhsf");
//        $offer2->setStatus(0);
//        $offer2->setRequest($request);
//        
//        $transaction = new Transaction();
//        $transaction->setDate(new DateTime("NOW"));
//        $transaction->setOffer($offer);
//        
//        $message = new Message();
//        $message->setDate(new DateTime("NOW"));
//        $message->setOffer($offer);
//        $message->setSender($user);
//        
//        $messageNotification = new NewMessageNotification();
//        $messageNotification->setCreated(new Datetime("NOW"));
//        $messageNotification->setSeen(0);
//        $messageNotification->setUser($user);
//        $messageNotification->setMessage($message);
//        
//        $transactionNotifiacation = new TransactionNotification();
//        $transactionNotifiacation->setCreated(new Datetime("NOW"));
//        $transactionNotifiacation->setSeen(0);
//        $transactionNotifiacation->setTransaction($transaction);
//        $transactionNotifiacation->setUser($user);
//        
//        $priceChangeNotification = new PriceChangeNotification();
//        $priceChangeNotification->setCreated(new DateTime("NOW"));
//        $priceChangeNotification->setOldPrice(10);
//        $priceChangeNotification->setNewPrice(100);
//        $priceChangeNotification->setRequest($request);
//        $priceChangeNotification->setUser($user);
//        $priceChangeNotification->setSeen(0);
//        
//        $session = new Session();
//        $session->setCreated(new DateTime("NOW"));
//        $session->setExpired(0);
//        $session->setUser($user);
//        $session->setSessionId("sdakjasdfhlkajsdfhasdf");
//                        
//        $doctrineManger->persist($user);
//        $doctrineManger->persist($userEmail);
//        $doctrineManger->persist($tangle);
//        $doctrineManger->persist($userTangle);
//        $doctrineManger->persist($request);
//        $doctrineManger->persist($tag1);
//        $doctrineManger->persist($tag2);
//        $doctrineManger->persist($offer);
//        $doctrineManger->persist($offer2);
//        $doctrineManger->persist($transaction);
//        $doctrineManger->persist($transactionNotifiacation);
//        $doctrineManger->persist($priceChangeNotification);
//        $doctrineManger->persist($message);
//        $doctrineManger->persist($messageNotification); 
//        $doctrineManger->persist($session); 
//        $doctrineManger->flush();
//        
//        return new Response("Created" , 201);
//    }
   
   
}
