<?php

namespace Megasoft\EntangleBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\False;

class PasswordForgetController extends Controller{

    /**
     * this function validates that the user credentials are correct
     * @param $name
     * @param $email
     * @return int
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
     * this function is the endpiont that returns the user password if he forgets it
     * @param Request $request
     * @return JsonResponse
     * @author KareemWahby
     */
    public function retrievePasswordAction(Request $request){
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
            $UserRepo = $doctrine->getRepository('MegasoftEntangleBundle:User');
            $user = $UserRepo->findOneBy(array('id' => $id));
            $password=$user->getPassword();
            $response->setStatusCode(200);
            $response->setContent(json_encode(array('password'=> $password)));
            return $response;
        }

    }
} 