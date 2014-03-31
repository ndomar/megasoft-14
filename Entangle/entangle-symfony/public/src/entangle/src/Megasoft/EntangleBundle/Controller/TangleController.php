<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TangleController extends Controller
{
    /**
      * Validates that the request has correct format, session Id is active and of a user and that the user is in the tangle
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $tangleId
      * @return \Symfony\Component\HttpFoundation\Response, null if no error exists
      */
    private function verifyUser($request, $tangleId){
        $sessionId = $request->headers->get('X-SESSION-ID');
        
        if($tangleId == null || $sessionId == null){
            return new Response('Bad Request', 400);
        }
        
        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');
        
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if($session == null || $session->getExpired()){
            return new Response('Bad Request', 400);
        }
        
        $user = $session->getUser();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'userId' => $user->getId()));
        
        if($userTangle == null){
            return new Response('Unauthorized', 401);
        }
        
        return null;
    }
    
   /**
      * An endpoint to return the list of users in a specific tangle
      * @param \Symfony\Component\HttpFoundation\Request $request
      * @param integer $tangleId
      * @return \Symfony\Component\HttpFoundation\Response
      */
    public function allUsersAction(\Symfony\Component\HttpFoundation\Request $request, $tangleId){
        $verification = $this->verifyUser($request, $tangleId);
        
        if($verification != null){
            return $verification;
        }
        
        $doctrine = $this->getDoctrine();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangles = $userTangleRepo->findBy(array('tangleId' => $tangleId));
        
        $usersJsonArray = array();
        
        foreach($userTangles as $userTangle){
            $usersJsonArray[] = array(
                                    'id' => $userTangle->getUserId(),
                                    'username' => $userTangle->getUser()->getName(),
                                    'balance' => $userTangle->getCredit(),
                                    'iconUrl' => $userTangle->getUser()->getPhoto()
                                );
        }
        
        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($usersJsonArray), 'users' => $usersJsonArray));
        
        return $response;
    }

}