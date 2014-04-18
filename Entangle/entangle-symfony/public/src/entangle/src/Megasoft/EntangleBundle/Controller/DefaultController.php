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

class DefaultController extends Controller {

    public function indexAction($name) {
        return $this->render('MegasoftEntangleBundle:Default:index.html.twig', array('name' => $name));
    }


// Please don't forget to do a register controller instead of the default controller #ESLAMMAGED
    public function registerAction(\Symfony\Component\HttpFoundation\Request $request)  {
        if ($request->getMethod() == 'POST') {  //reading the request object and getting data out of it
            //$username = $request->get('username');
            $name = $request->get('name');
            $password = $request->get('password');
            $confirmPassword = $request->get('confirmPassword'); //Remember to do the check matching passwords
            $email = $request->get('email');
            $userBio = $request->get('userBio');
            $birthDate = new \DateTime($request->get('birthDate'));
            $verified = false;
            $user = new User;
            $userEmail = new UserEmail();

            $user->addEmail($userEmail);
            $user->setName($name);
            $user->setPassword($password);
            $user->setUserBio($userBio);

            $user->setBirthDate($birthDate);
            $user->setVerified($verified);
            $userEmail->setEmail($email);

            $image = $request->files->get('img');
            $status = 'succes';
            $uploadedURL = '';
            $message='';
            if(($image instanceof UploadedFile)&&($image->getError() == '0')) {
                if($image->getSize() < 4194304) { //if image size is less that 4MB
                    $originalName = $image->getClientOriginalName();
                    $nameArray = explode('.', $originalName);
                    $fileType = $nameArray[sizeof($nameArray) - 1];
                    $validFileTypes = array('jpg', 'jpeg' , 'bmp' ,
                    'png');

                    if(in_array(strtolower($fileType), $validFileTypes)) {

                        $filepath = '/home/neuron/Documents/megasoft-14/Entangle/entangle-symfony/public/src/entangle/web/images/profilePictures/' . substr(md5(time()),0,10) . '.' .$fileType;
                        move_uploaded_file($image,$filepath);
                        $user->setPhoto($filepath);

                    }
                    else {
                        $status = 'failed';
                        $message = 'Invalid File Type';
                    }
                }
                else {
                    $status = 'failed';
                    $message = 'Size limit exceeded';
                }
            }
            else {
                $status = 'failed';
                $message = 'File Error';
            }





            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->persist($user);
            $entityManager->persist($userEmail);
            $entityManager->flush();

            return new Response("Created" , 201);
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

//        $invitationMessage = new InvitationMessage();
//        $invitationMessage->setBody("test");

//        $pendingInvitation = new PendingInvitation();
//        $pendingInvitation->setInvitee($doctrineManger->getReference('MegasoftEntangleBundle:User',1));
//        $pendingInvitation->setInviter($doctrineManger->getReference('MegasoftEntangleBundle:User',2));
//        $pendingInvitation->setTangle($doctrineManger->getReference('MegasoftEntangleBundle:Tangle',1));
//        $pendingInvitation->setMessage($doctrineManger->getReference('MegasoftEntangleBundle:InvitationMessage',2));

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
//        $doctrineManger->persist($invitationMessage); 
//        $doctrineManger->persist($pendingInvitation); 
//        $doctrineManger->flush();
//          
//          return new Response("Created" , 201);
//    }
}
