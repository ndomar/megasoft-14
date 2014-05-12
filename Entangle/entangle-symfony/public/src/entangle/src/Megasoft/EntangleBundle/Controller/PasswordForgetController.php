<?php

namespace Megasoft\EntangleBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\False;

class PasswordForgetController extends Controller{
    /**
     * This Function generates a Random String to be used as the new Password
     * @return string
     * @author KareemWahby
     */
    private function randPassGen(){
        $newpass = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < 5; $i++) {
            $newpass .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $newpass;
    }

    /**
     * This function takes the user name and email and checks if the user with these attributes is a valid user
     * @param $name
     * @param $email
     * @return int user id if succsesful -1 if not
     * @author KareemWahby
     */
    private function securityCheck($name,$email) {
        if ($name == null ||$email == null) {
            return -1;
        }
        $doctrine = $this->getDoctrine();
        $UserRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $emailRepo= $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $user = $UserRepo->findOneBy(array('name' => $name));
        $userEmail= $emailRepo->findOneBy(array('email' => $email));
        if($user == null || $userEmail== null){
            return -1;
        }else{
            $userId= $user-> getId();
            $userIdByMail= $userEmail-> getUserID();
            if($userId != $userIdByMail){
                return -1;
            }
            return $userId;
        }

    }

    /**
     * This is the endpoint responsible for resetting the password for the user and send him an email with the new password
     * @param Request $request
     * @return JsonResponse
     * @author KareemWahby
     */
    public function resetPasswordAction(Request $request){
        $data = json_decode($request->getContent(),true);
        $name = $data['name'];
        $email= $data['email'];
        $response= new JsonResponse();
        $id=$this->securityCheck($name,$email);
        if($id == -1){
            $response->sendContent("Bad Request");
            $response->setStatusCode(400);
            return $response;
        }else{
            $doctrine = $this->getDoctrine();
            $em=$doctrine->getEntityManager();
            $UserRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
            $user = $UserRepo->findOneBy(array('id' => $id));
            $newPass=$this->randPassGen();
            $user->setPassword($newPass);
            $em->persist($user);
            $em->flush();
            $message = \Swift_Message::newInstance()
                ->setSubject('Entangle Password Reset')
                ->setFrom('kareem.wahby@gmail.com')
                ->setTo($email)
                ->setBody('Hello '.$name.',

It seems like you\'ve forgotten your password, here is your new one, '.$newPass.' just make sure you CHANGE IT ASAP.

Cheers,
Entangle Team.')
            ;
            $this->get('mailer')->send($message);
            $response->setStatusCode(200);
            $response->setContent('Email Sent Successfully');
            return $response;
        }

    }
} 