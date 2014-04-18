<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Request;

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
            if ($tangleRequest->getStatus() == Request::OPEN) {
                return new Response("Request is already open", 400);
            }
        }
        
        $tangleRequest->setStatus(Request::OPEN);
        $this->getDoctrine()->getManager()->persist($tangleRequest);
        $this->getDoctrine()->getManager()->flush();
    }

}
