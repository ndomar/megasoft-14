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

class RegisterController extends Controller {

    public function indexAction($name) {
        return $this->render('MegasoftEntangleBundle:Register:register.html.twig', array('name' => $name));
    }


    private function generate($len){
        $ret = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789";
        for($i=0;$i<$len;$i++){
            $ret .= $seed[rand(0,strlen($seed)-1)];
        }
        return $ret;
    }

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
            if($name != null && $name != "") {
                $user->setName($name);
            }
            if($password == $confirmPassword ) {
                $user->setPassword($password);
            }

            if($userBio!=null && $userBio!="") {
                $user->setUserBio($userBio);
            }

            if($birthDate != null && $birthDate!= ""){
                $user->setBirthDate($birthDate);
            }

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

                        $filepath = '/home/neuron/Documents/megasoft-14/Entangle/entangle-symfony/public/src/entangle/web/images/profilePictures/' . substr(md5(time()),0,10) . $this->generate(5) . '.' .$fileType;
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
        return $this->render('MegasoftEntangleBundle:Register:register.html.twig');
    }




}
