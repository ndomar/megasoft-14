<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UnfreezeController extends Controller {

    public function getUnfreezeReq() {
        $doctrine = $this->getDoctrine();
        $UnfreezeRepo = $doctrine->getRepository('MegasoftEntangleBundle:UnfreezeRequest');
        $UnfeeezeRequestsJson = array();
        foreach ($UnfreezeRepo as $UnfeeezeR) {
            $UnfeeezeRequestsJson[] = array(
                'UnfreezeReqid' => $UnfeeezeR->getId(),
                'UserId' => $UnfeeezeR->getUserId(),
                'requestId' => $UnfeeezeR->getRequestId()
            );
        }
        $response = new JsonResponse();
        $response->setData($UnfeeezeRequestsJson,200);
        return $response;
    }
    
    public function createRequest($request){
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
        $unfreezeReq = array(
        'UserId' => $request->request->get('UserId'),
        'requestId'  => $request->request->get('requestId'),
        );
        $newUnfreeze= new \Megasoft\EntangleBundle\Entity\UnfreezeRequest();
        $newUnfreeze.setUserId($unfreezeReq->get('UserId'));
        $newUnfreeze.setRequestId($unfreezeReq->get('requestId'));
        return Response(201);
    }
    public function removeRequest($unfreezeReqid){
        $em= $this->getDoctrine()->getManager();
        $repo=$this->getDoctrine()->getRepository('MegasoftEntangleBundle:UnfreezeRequest');
        $unfreezeR=$repo->findOneBy($unfreezeReqid);
        $em->remove($unfreezeR);
        return Response(204);
    }
    public function getFP($unfreezeReqid) {
        $doctrine = $this->getDoctrine();
        $UnfreezeRepo = $doctrine->getRepository('MegasoftEntangleBundle:UnfreezeRequest');
        $Ureq = $UnfreezeRepo->findOneBy($unfreezeReqid);
        $requestid= $Ureq->getRequestId();
        //cannot find price ??
        //unfinished
    }
    public function restoreFP($unfreezeReqid) {
        //unfinished
    }
}
