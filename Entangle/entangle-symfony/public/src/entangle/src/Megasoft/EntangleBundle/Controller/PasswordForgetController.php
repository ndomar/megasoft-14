<?php

namespace Megasoft\EntangleBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\False;

class PasswordReset extends Controller{

    /**
     * this function checks that the data of the user is valid so a new password could be generated
     * @param $name
     * @param $email
     * @param $dob
     * @return bool true if the security checks are passed false otherwise
     * @author KareemWahby
     */
    private function securityCheck($name,$email,$dob) {
        if ($name == null ||$email == null || $dob == null) {
            return false;
        }
        $doctrine = $this->getDoctrine();
        $UserRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $emailRepo= $doctrine->getRepository('MegasoftEntangleBundle:UserEmail');
        $user = $UserRepo->findOneBy(array('name' => $name,'birthDate' => $dob));
        if ($user == null) {
            return false;
        }
        $userId= $user-> getUserId();
        $userEmail= $emailRepo->findOneBy(array('userId' => $userId));
        $mail= $userEmail->getEmail();
        if($email != $mail){
           return false;
        }
        return true;
    }

    /**
     * this is the endpoint for reseting the password of the user
     * @param Request $request
     * @return JsonResponse
     * @author KareemWahby
     */
    public function retrievePassword(Request $request){
        $data = $request->getContent($request,true);
        $name = $data['name'];
        $email= $data['email'];
        $dob= $data['birthDate'];
        $response= new JsonResponse();
        if(!$this->securityCheck($name,$email,$dob)){
            $response->sendContent("Bad Request");
            $response->setStatusCode(400);
            return $response;
        }
        $doctrine = $this->getDoctrine();
        $UserRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user = $UserRepo->findOneBy(array('name' => $name,'birthDate' => $dob));
        $password=$user->getPassword();
        $response->setStatusCode(200);
        $response->setData(array('password'=> $password));
        return $response;
    }
} 