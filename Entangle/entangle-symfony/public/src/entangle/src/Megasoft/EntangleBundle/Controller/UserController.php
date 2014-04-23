<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class UserController extends Controller {

    /**
     * Validates the username and password from request and returns sessionID
     * @param  Integer $len length for the generated sessionID
     * @return String $generatedSessionID the session id that will be used
     * 
     * @author maisaraFarahat
     */
    private function generateSessionId($len) {
        $generatedSessionID = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < $len; $i++) {
            $generatedSessionID .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $generatedSessionID;
    }

    /**
     * Validates the username and password from request and returns sessionID
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response $response
     * 
     * @author maisaraFarahat
     */
    public function loginAction(\Symfony\Component\HttpFoundation\Request $request) {
        $response = new JsonResponse();
        $badReq = "bad request";
        if (!$request) {
            $response->setStatusCode(400, $badReq);
            return $response;
        }
        $json = $request->getContent();
        if (!$json) {
            $response->setStatusCode(400, $badReq);
            return $response;
        }
        $json_array = json_decode($json, true);
        $name = $json_array['name'];
        $password = md5($json_array['password']);

        if (!$name) {
            $response->setStatusCode(400, "missing name");
            return $response;
        }
        if (!$password) {
            $response->setStatusCode(400, "missing password");
            return $response;
        }
        if (strstr("\"", $name) || strstr("'", $name)) {
            $response->setStatusCode(400, "name has special characters");
            return $response;
        }
        $sessionId = $this->generateSessionId(30);

        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->findOneBy(array('name' => $name, 'password' => $password));

        $session = new Session();
        $session->setSessionId($sessionId);
        $session->setUser($user);
        $session->setUserId($user->getId());
        $session->setCreated(new \DateTime('tomorrow'));
        $session->setExpired(0);
        $session->setRegId("ToAvoidNull");
        $session->setDeviceType("Galaxy S3");

        $user->addSession($session);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($session);

        $this->getDoctrine()->getManager()->flush();
        $response->setData(array('sessionId' => $sessionId));
        $response->setStatusCode(201);
        return $response;
    }

}
