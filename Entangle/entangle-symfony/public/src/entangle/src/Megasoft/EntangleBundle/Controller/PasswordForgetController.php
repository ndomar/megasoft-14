<?php

namespace Megasoft\EntangleBundle\Controller;


use Megasoft\EntangleBundle\Entity\ForgetPasswordCode;
use Megasoft\EntangleBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Tests\Extension\Core\Type\RepeatedTypeTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordForgetController extends Controller{
    /**
     * This Function generates a Random String to be used as the link to reset password
     * @return string
     * @author KareemWahby
     */
    private function randPassCodeGen(){
        $code = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < 30; $i++) {
            $code .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $code;
    }


    private function securityCheck($email) {
        if ($email == null) {
            return -1;
        }
        $doctrine = $this->getDoctrine();
        $emailRepo= $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $userEmail= $emailRepo->findOneBy(array('email' => $email));
        if($userEmail== null){
            return -1;
        }else{
            $userId= $userEmail-> getUserID();
            return $userId;
        }

    }


    public function forgetPasswordAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $email= $data['email'];
        $response= new Response();
        $id=$this->securityCheck($email);

        if($id == -1){
            $response->setStatusCode(400);
            return $response;
        }else{

            $random=$this->randPassCodeGen();
            $link="http://entangle.io/reset/".$random;
            $doctrine = $this->getDoctrine();
            $user = $doctrine->getRepository('MegasoftEntangleBundle:User')->findOneBy(array("id" => $id));
            $userName = $user->getName();
            $em=$doctrine->getManager();
            $passwordCodeCheck=$doctrine->getRepository('MegasoftEntangleBundle:ForgetPasswordCode')->findOneBy(array("userId" => $id));
            if($passwordCodeCheck==null){
                $passwordCode = new ForgetPasswordCode();
                $passwordCode->setCreated(new \DateTime('now'));
                $passwordCode->setUser($user);
                $passwordCode->setExpired(0);
                $passwordCode->setForgetPasswordCode($random);
                $em->persist($passwordCode);
                $em->flush();
            }else{
                $passwordCodeCheck->setForgetPasswordCode($random);
                $em->persist($passwordCodeCheck);
                $em->flush();
            }
            $message = "It seems like you've forgotten your password, you can reset it by using this ";
            $title = "Entangle Password Reset";
            $body = "<!DOCTYPE html>
                <html lang=\"en\">
                    <head>
                    </head>
                    <body>
                      <h3>
                        Hello, ".$userName."
                      </h3>
                      <p>" . $message . "<a href=\"".$link."\">link</a>
                      <p>Cheers,<br>Entangle Team</p>
                </html>";

            $notificationCenter = $this->get('notification_center.service');
            $notificationCenter->sendMail($id, $title, $body);
            $response->setStatusCode(200);
            return $response;
        }
    }

    public function resetPasswordAction($passCode){
        $doctrine = $this->getDoctrine();
        $em=$doctrine->getManager();
        $ForgetPasswordCodeRepo= $doctrine->getRepository('MegasoftEntangleBundle:ForgetPasswordCode');
        $ForgetPasswordCode=$ForgetPasswordCodeRepo->findOneBy(array('forgetPasswordCode'=>$passCode));
        if($ForgetPasswordCode== null){
            return $this->render('MegasoftEntangleBundle:ForgetPassword:404.html.twig');
        }
        if($ForgetPasswordCode->getExpired()== 1){
            return $this->render('MegasoftEntangleBundle:ForgetPassword:expired.html.twig');
        }else{
            /* @var User $user */
            $userName=$ForgetPasswordCode->getUser()->getName();
            $ForgetPasswordCode->setExpired(1);
            $em->persist($ForgetPasswordCode);
            $em->flush();
            return $this->render('MegasoftEntangleBundle:ForgetPassword:passwordChangeform.html.twig',array('error'=> "",'status'=> "",'userName'=> $userName));
        }

    }
    public function changePasswordAction(Request $request){
        $userName=$request->get('Username');
        //return new Response($userName);
       $message="";
        $status="";
        $doctrine = $this->getDoctrine();
        $em=$doctrine->getManager();
        $userRepo= $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user=$userRepo->findOneBy(array("name"=>$userName));
        if ($request->getMethod() == 'POST') {

            $password = $request->get('newPass');
            $retypedPassword = $request->get('newPassR');
            if($password==null || $retypedPassword==null){
                $message = "Please fill ALL fields!!";
                return $this->render('MegasoftEntangleBundle:ForgetPassword:passwordChangeform.html.twig',array('error'=> $message,'status'=> $status,'userName'=> $userName));
            }

            if ($password == $retypedPassword) {

                $user->setPassword($password);
                $em->persist($user);
                $em->flush();
                $status = "Password Changed Successfully!";
                return $this->render('MegasoftEntangleBundle:ForgetPassword:passwordChangeform.html.twig',array('error'=> $message,'status'=> $status,'userName'=> $userName));

            }else{
                $message = "Passwords Do Not Match!!";
                return $this->render('MegasoftEntangleBundle:ForgetPassword:passwordChangeform.html.twig',array('error'=> $message,'status'=> $status,'userName'=> $userName));
            }

        }
        return $this->render('MegasoftEntangleBundle:ForgetPassword:passwordChangeform.html.twig',array('error'=> "",'status'=> "",'userName'=> $userName));
    }
} 