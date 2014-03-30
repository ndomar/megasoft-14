<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClaimController extends Controller
{

    /**
     * @param $claimID
     * @return JsonResponse|Response
     */
    public function contactAction($claimID)
    {
        $claim = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Claim")->find($claimID);

        if (!$claim) {
            return new Response("claim not found", Response::HTTP_NOT_FOUND);
        } else {
            $arr = array(
                "requesterID" => $claim->getUser(),
                "requesterName" => getUserName($claim->getUser()),
                "requestDesc" => "ma tgeb 100 gneh 3shan masr",
                "offererID" => 6,
                "offererName" => "shaban",
                "offerDesc" => "5od yad :D",
                "claimMessage" => $claim->getMessage,
            );
            return new JsonResponse($arr);
        }
    }

    /**
     * @param $userid
     * @return mixed
     */
    public function getUserName($userid)
    {
        $user = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:user")->find($userid);
        return $user->getName();
    }


    /**
     * @return JsonResponse|Response
     */
    public function sendAction()
    {
        $content = $this->get("request")->getContent();
        if (!(empty($content))) {
            $vals = json_decode($content, true);
            sendMessage($vals["userID"], $vals["message"]);
            $arr = array("message" => "message was sent successfully");
            return new JsonResponse($arr);
        } else {
            return new Response("invalid data", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * this method will be invoked when we finish messaging system
     *
     * @param $userid
     * @param $message
     */
    public function sendMessage($userid, $message)
    {

    }

}
