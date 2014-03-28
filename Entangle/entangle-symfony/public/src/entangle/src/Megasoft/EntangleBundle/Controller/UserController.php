<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    private function generate($len) {
        $ret = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < $len; $i++) {
            $ret .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $ret;
    }

    public function createAction(Request $request) {
        $response = new JsonResponse();
        if (!$request) {
            $response->setStatusCode(400);
            $response->setContent("Did not Receive Request");
            return $response;
        }
        $json = $request->getContent();
        if (!$json) {
            $response->setStatusCode(400);
            $response->setContent("the request was null!");
            return $response;
        }
        $json_array = json_decode($json, true);
        $username = $json_array['username'];
        $password = md5($json_array['password']);


        if (!$username) {

            $response->setStatusCode(400);
            $response->setContent("Missing Username");
            return $response;
        }
        if (!$password) {
            $response->setStatusCode(400);
            $response->setContent("Missing Password");
            return $response;
        }
        if (strstr("\"", $username) || strstr("'", $username)) {
            $response->setStatusCode(400);
            $response->setContent("youm ma kont raye7 tesee3 kont ana bal3ab henak");
        }
        $sessionId = $this->generate(30);

        $user = new User();

        $user->setSessionId($sessionId);
        $user->setUsername($username);
        $user->setPassword($password);


        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        $response->setData(array('sessionId' => $sessionId));
        $response->setStatusCode(201);
        return $response;
    }

}
