<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response $response
     * 
     * @author maisaraFarahat
     */
    public function loginAction(Request $request) {
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

    /**
     * checks for the sessionID and gets the user 
     * @param \Symfony\Component\HttpFoundation\Request $request request containing the sessionId
     * @return \Symfony\Component\HttpFoundation\Response $response response containing: 
     * user with sessionID , date of birth , emails , userID and description
     * 
     * @author maisaraFarahat
     */
    public function whoAmIAction(Request $request) {
        $json = $request->getContent();
        if (!json)
            return new Response(400, 'request was null');
        $json_array = json_decode($json, true);
        $sessionId = $json_array['session_id'];
        if (!$sessionId) {
            return new Response(400, 'sessionID was null');
        } else {
            $doctrine = $this->getDoctrine();
            $repo = $doctrine->getRepository('MegasoftEntangleBundle:User');
            $retrievedSession = $repo->findOneBy(array('sessionId' => $sessionId));
            $user = $retrievedSession->getUser();
            if ($user == null) {
                return new Response('Bad Request', 400);
            } else {

                $response = new JsonResponse();
                $emails = $user->getEmails();
                $description = $user->getUserBio();
                $username = $user->getName();
                $dob = $user->getBirthDate();
                $userId = $user->getId();

                $response->setData(array('user' => $user, 'user_id' => $userId,
                    'date_of_birth' => $dob, 'description' => $description,
                    'username' => $username, 'emails' => $emails));

                $response->setStatusCode(200);
                return $response;
            }
        }
    }

}
