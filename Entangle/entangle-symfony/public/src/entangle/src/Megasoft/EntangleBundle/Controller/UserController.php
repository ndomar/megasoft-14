<?php

namespace Megasoft\EntangleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller {

    private function generateSessionId($len) {
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
            return new Response('Bad Request', 400);
        }
        $json = $request->getContent();
        if (!$json) {
            return new Response('Bad JSON Request', 400);
        }
        $json_array = json_decode($json, true);
        $username = $json_array['username'];
        $password = md5($json_array['password']);

        if (!$username) {
            return new Response('Missing Username', 400);
        }
        if (!$password) {
            return new Response('Missing Password', 400);
        }
        if (strstr("\"", $username) || strstr("'", $username)) {
            return new Response('
            usernames should not have any special characters', 400);
        }
        $sessionId = $this->generateSessionId(30);

        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->findOneBy(array('username' => $username, 'password' => $password));

        $user->setSessionId($sessionId);



        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        $response->setData(array('sessionId' => $sessionId));
        $response->setStatusCode(201);
        return $response;
    }

    public function whoAmIAction($sessionId) {
        if (!$sessionId)
            return new Response(400, 'sessionID was null');
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->findOneBy(array('sessionId' => $sessionId));
        if ($user == null) {
            return new Response('Bad Request', 400);
        } else {
            $response = new JsonResponse();
            $response->setData(array('user' => $user));
            $response->setStatusCode(200);
            return $response;
        }
    }

}
