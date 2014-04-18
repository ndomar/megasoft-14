<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestController extends Controller {

    public function reOpenRequestAction(Request $request, $requestId) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }
        if ($session == null) {
            return new Response("Unauthorized", 401);
        }
        $requestRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Request');
        $tangleRequest = $requestRepo->findOneBy(array('id' => $requestId));
        if ($tangleRequest == null) {
            return new Response("Bad Request", 400);
        } else {
            if ($tangleRequest->getStatus() == \Megasoft\EntangleBundle\Entity\Request::OPEN) {
                return new Response("Request is already open", 400);
            }
        }
        if($tangleRequest->getStatus() == \Megasoft\EntangleBundle\Entity\Request::CLOSED) {
            $tangleRequest->setStatus(\Megasoft\EntangleBundle\Entity\Request::OPEN);
            $this->getDoctrine()->getManager()->persist($tangleRequest);
            $this->getDoctrine()->getManager()->flush();
            return new Response('Reopened', 200);
        }
    }

}
