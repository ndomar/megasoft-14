<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\Tangle;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\InvitationCode;
use Megasoft\EntangleBundle\Entity\InvitationMessage;
use Megasoft\EntangleBundle\Entity\PendingInvitation;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;

class TangleController extends Controller {

    /**
     * Validates that the request has correct format,  session Id is active and of a user and that the user is in the tangle
     * @param Request $request
     * @param integer $tangleId
     * @return Response | null
     * @author OmarElAzazy
     */
    private function verifyUser($request, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($tangleId == null || $sessionId == null) {
            return new Response('Bad Request', 400);
        }

        $doctrine = $this->getDoctrine();
        $sessionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');

        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        if ($session == null || $session->getExpired()) {
            return new Response('Bad Request', 400);
        }

        $user = $session->getUser();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangle = $userTangleRepo->findOneBy(array('tangleId' => $tangleId, 'userId' => $user->getId()));

        if ($userTangle == null) {
            return new Response('Unauthorized', 401);
        }

        return null;
    }

    /**
     * An endpoint to filter requests of a specific tangle by requester, tag, prefix of requester's name or description
     * @param Request $request
     * @param integer $tangleId
     * @return Response | Symfony\Component\HttpFoundation\JsonResponse
     * @author OmarElAzazy
     */
    public function filterRequestsAction(Request $request, $tangleId) {
        $verification = $this->verifyUser($request, $tangleId);

        if ($verification != null) {
            return $verification;
        }

        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');

        $query = $requestRepo->createQueryBuilder('request')

                ->where('request.tangleId = :tangleId')
                ->setParameter('tangleId', $tangleId)
                ->andWhere('request.deleted = :false')
                ->setParameter('false', false);

        $userId = $request->query->get('userid', null);
        if ($userId != null) {
            $query = $query->andWhere('request.userId = :userId')
                ->setParameter('userId', $userId);
        }

        $fullText = $request->query->get('fulltext', null);
        if ($fullText != null) {
            $query = $query->andWhere('request.description LIKE :fullTextFormat')
                ->setParameter('fullTextFormat', '%' . $fullText . '%');
        }


        $usernamePrefix = $request->query->get('usernameprefix', null);
        if ($usernamePrefix != null) {
            $query = $query->innerJoin('MegasoftEntangleBundle:User', 'user', 'WITH', 'request.userId = user.id')
                ->andWhere('user.name LIKE :usernamePrefixFormat')
                ->setParameter('usernamePrefixFormat', $usernamePrefix . '%');
        }

        $requests = $query->getQuery()->getResult();

        $tagId = $request->query->get('tagid', null);
        $requestsJsonArray = array();


        foreach ($requests as $tangleRequest) {

            if ($tagId != null) {
                $foundTag = false;
                foreach ($tangleRequest->getTags() as $tag) {
                    if ($tag->getId() == $tagId) {
                        $foundTag = true;
                        break;
                    }
                }


                if (!$foundTag) {
                    continue;
                }
            }

            $requestsJsonArray[] = array(
                'id' => $tangleRequest->getId(),
                'username' => $tangleRequest->getUser()->getName(),
                'userId' => $tangleRequest->getUserId(),
                'description' => $tangleRequest->getDescription(),
                'offersCount' => sizeof($tangleRequest->getOffers()),
                'price' => $tangleRequest->getRequestedPrice()
            );
        }

        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($requestsJsonArray), 'requests' => $requestsJsonArray));

        return $response;
    }

    /**
     * An endpoint to return the list of tags in a specific tangle
     * @param Request $request
     * @param integer $tangleId
     * @return Response | Symfony\Component\HttpFoundation\JsonResponse
     * @author OmarElAzazy
     */

    public function allTagsAction(Request $request, $tangleId) {
        $verification = $this->verifyUser($request, $tangleId);

        if ($verification != null) {
            return $verification;
        }

        $doctrine = $this->getDoctrine();
        $requestRepo = $doctrine->getRepository('MegasoftEntangleBundle:Request');
        $criteria = array('tangleId' => $tangleId, 'deleted' => false);
        $requests = $requestRepo->findBy($criteria);

        $tags = array();
        foreach ($requests as $tangleRequest) {
            $tags = array_merge($tags, $tangleRequest->getTags()->toArray());
        }

        $tags = array_unique($tags);

        $tagsJsonArray = array();


        foreach ($tags as $tag) {
            $tagsJsonArray[] = array(
                'id' => $tag->getId(),
                'name' => $tag->getName()
            );
        }

        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($tagsJsonArray), 'tags' => $tagsJsonArray));

        return $response;
    }

    /**
     * Validates the existance of a certain tangle
     * @param integer $tangleId
     * @return boolean true if the tangle exists , false otherwise
     * @author MohamedBassem
     */
    private function validateTangleId($tangleId) {
        $tangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Tangle');
        if ($tangleRepo->findOneById($tangleId) == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates that the email is a valid email
     * @param string $email
     * @return boolean true if the email is valid , false otherwise
     * @author MohamedBassem
     */
    private function isValidEmail($email) {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * checks if this email is an entangle member or not
     * @param string $email
     * @return boolean true if the member is new , false otherwise
     * @author MohamedBassem
     */
    private function isNewMember($email) {
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $mail = $userEmailRepo->findOneBy(array('email' => $email, 'deleted' => false));
        return ($mail == null);
    }

    /**
     * Checks if a certain email belongs to a user that is in the tangle
     * @param integer $email
     * @param integer $tangleId
     * @return boolean true if the user belongs to the tangle , false otherwise
     * @author MohamedBassem
     */
    private function isTangleMember($email, $tangleId) {
        $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');
        $mail = $userEmailRepo->findOneByEmail($email);
        $userId = $mail->getUserId();
        return ($userTangleRepo->findOneBy(array('userId' => $userId, 'tangleId' => $tangleId)) != null);
    }

    /**
     * This function is used to send the invitation mail to $email with the message $message and
     * creates the invitation code and send it to the user
     * @param string $email
     * @param integer $inviterId
     * @param string $message
     * @author MohamedBassem
     */

    
    public function inviteUser($email,$tangleId,$inviterId,$message){
        $randomString = $this->generateRandomString(30);

        $tangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $tangleRepo->findOneById($tangleId);

        $newInvitationCode = new InvitationCode();
        $newInvitationCode->setCode($randomString);
        if ($this->isNewMember($email)) {
            $newInvitationCode->setUserId(null);
        } else {
            $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
            $user = $userEmailRepo->findOneByEmail($email)->getUser();
            $newInvitationCode->setUser($user);
        }

        $newInvitationCode->setTangle($tangle);
        $newInvitationCode->setInviterId($inviterId);
        $newInvitationCode->setExpired(false);
        $newInvitationCode->setCreated(new DateTime("NOW"));
        $newInvitationCode->setEmail($email);

        $this->getDoctrine()->getManager()->persist($newInvitationCode);
        $this->getDoctrine()->getManager()->flush();

        $title = "you are invited to bla";
        $body = "<!DOCTYPE html>
                <html lang=\"en\">
                    <head>
                    </head>
                    <body>
                           <h3>
                                Hello
                           </h3>
                           <p>" . $message . "</p>
                           <a href=\"http://entangle.io/invitation/" . $randomString . "\">link</a>
                           <p>Cheers<br>Entangle Team</p>
                    </body>
                </html>";


        
        $notificationCenter = $this->get('notification_center.service');
        $notificationCenter->sendMailToEmail($email, $title, $body);
    }

    /**
     * An endpoint to invite a list of emails to join a certain tangle
     * it creates the invitation code and send it to the user
     * @param Request $request
     * @param integer $tangleId
     * @return Response
     * @author MohamedBassem
     */
    public function inviteAction(Request $request, $tangleId) {

        $sessionId = $request->headers->get('X-SESSION-ID');

        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }

        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');

        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response("Unauthorized", 401);
        }

        if (!$this->validateTangleId($tangleId)) {
            return new Response("Tangle Not Found", 404);
        }

        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        if (($userTangle = $userTangleRepo->findOneBy(array('userId' => $session->getUserId(), 'tangleId' => $tangleId))) == null) {
            return new Response("You are not a tangle member to invite other members", 401);
        }

        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);

        if (!isset($json['emails']) || !isset($json['message']) || !is_array($json['emails'])) {
            return new Response("Bad Request", 400);
        }

        $isOwner = $userTangle->getTangleOwner();

        foreach ($json['emails'] as $email) {

            if ( !$this->isValidEmail($email) || (!$this->isNewMember($email) && $this->isTangleMember($email, $tangleId)) ) {
                continue;
            }

            if ($isOwner) {
                $this->inviteuser($email,$tangleId,$session->getUserId(),$json['message']);
            } else {
                $em = $this->getDoctrine()->getManager();

                $invitationMessage = new InvitationMessage();
                $invitationMessage->setBody($json['message']);

                $pendingInvitation = new PendingInvitation();
                if ($this->isNewMember($email)) {
                    $pendingInvitation->setInvitee(null);
                } else {

                    $userEmailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
                    $user = $userEmailRepo->findOneByEmail($email)->getUser();
                    $pendingInvitation->setInvitee($user);
                }

                $pendingInvitation->setInviter($session->getUser());
                $pendingInvitation->setMessage($invitationMessage);
                $pendingInvitation->setTangle($userTangle->getTangle());
                $pendingInvitation->setEmail($email);

                $em->persist($invitationMessage);
                $em->persist($pendingInvitation);
                $em->flush();
            }
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setStatusCode(201);
        if($isOwner){
            $jsonResponse->setData(array('pending'=>0));
        }else{
            $jsonResponse->setData(array('pending'=>1));
        }
        return $jsonResponse;
    }

    /**
     * Generates a random string of length $len
     * @param integer $len
     * @return string a randomly generated string of length $len
     * @author MohamedBassem
     */
    private function generateRandomString($len) {
        $ret = '';
        $seed = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        for ($i = 0; $i < $len; $i++) {
            $ret .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $ret;
    }

    /**
     * Parse the request and creates a new tangle.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Mansour
     */
    public function createTangleAction(Request $request) {

        $json = $request->getContent();
        $json_array = json_decode($json, true);
        $tangleName = $json_array['tangleName'];
        $tangleIcon = $json_array['tangleIcon'];
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sessionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($sessionId == null || $tangleIcon == null || $tangleName == null) {
            return new Response("Bad Request", 400);
        }

        if ($session == null) {
            return new Response("Unauthorized", 401);
        }

        if (!($this->checkAvailability($tangleName))) {
            return new Response("Tangle Already Taken", 200);
        }
        $imageData = base64_decode($tangleIcon);
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
        if ($mimeType == false || $mimeType != 'image/png') {
            return new Response("Bad image", 400);
        }
        $icon = imagecreatefromstring($imageData);
        $iconName = $this->generateRandomString(50) . '.png';
        $kernel = $this->get('kernel');
        $path = $kernel->getRootDir() . '/../web/bundles/megasoftentangle/images/tangle/icons/' . $iconName;

        if ($icon != null) {
            imagepng($icon, $path, 9);
            imagedestroy($icon);
        }

        $tangle = new Tangle();
        $tangle->setName($tangleName);
        $tangle->setIcon($iconName);
        $tangle->setDeleted(false);
        $tangleOwner = new UserTangle();
        $tangleOwner->setUserId($session->getUserId());
        $tangleOwner->setCredit(0);
        $tangleOwner->setTangle($tangle);
        $tangleOwner->setTangleOwner(true);
        $tangleOwner->setUser($session->getUser());

        $this->getDoctrine()->getManager()->persist($tangle);
        $this->getDoctrine()->getManager()->persist($tangleOwner);
        $this->getDoctrine()->getManager()->flush();

        $response = new Response();
        $response->setStatusCode(201);
        
        return $response;
    }

    /**
     * Checks whether the tangle is already there or not.
     * 
     * @param type $tangleName
     * @return boolean
     * @author Mansour
     */
    public function checkAvailability($tangleName) {

        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository('MegasoftEntangleBundle:Tangle');
        $tangle = $repo->findOneBy(array('name' => $tangleName));

        if ($tangle == null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates whether the user with the session id $sessionId is the owner of the tangle with
     * tangle id $tangleId , If yes the function returns null, returns the appropriate exception otherwise
     * @param integer $sessionId
     * @param integer $tangleId
     * @return Response|null
     * @author MohamedBassem
     */
    public function validateIsOwner($sessionId, $tangleId) {
        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }

        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');

        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response("Unauthorized", 401);
        }

        $userTangleRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        if (($userTangle = $userTangleRepo->findOneBy(array('userId' => $session->getUserId(), 'tangleId' => $tangleId))) == null || !$userTangle->getTangleOwner()) {

            return new Response("Unauthorized", 401);
        }

        return null;
    }

    /**
     * A function that is responsible of verifing the request from
     * a user leaving a tangle
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @author HebaAamer
     */
    private function leaveTangleVerification($request, $tangleId) {
        $verification = $this->verifyUser($request, $tangleId);


        if ($verification != null) {
            return $verification;
        }

        $sessionId = $request->headers->get("X-SESSION-ID");

        $doctrine = $this->getDoctrine();

        $sessionRepo = $doctrine->getRepository("MegasoftEntangleBundle:Session");
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $userId = $session->getUserId();

        $userTangleRepo = $doctrine->getRepository("MegasoftEntangleBundle:UserTangle");
        if (($userTangle = $userTangleRepo
                ->findOneBy(array('userId' => $userId,
            'tangleId' => $tangleId, 'tangleOwner' => true))) != null) {
            return new Response("Forbidden", 403);
        }
        $userTangle = $userTangleRepo
                ->findOneBy(array('userId' => $userId,
            'tangleId' => $tangleId));
        if ($userTangle->getLeavingDate() != null) {
            return new Response("Unauthorized", 401);
        }

        return null;
    }

    /**
     * A function that is reponsible for deleting the requests
     * of a user that left the tangle
     *
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeRequests($tangleId, $userId) {
        $requestRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Request");

        $requests = $requestRepo->findBy(array('tangleId' => $tangleId,
            'userId' => $userId));
        $userRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:User");
        $user = $userRepo->find($userId);

        if ($requests != null) {
            foreach ($requests as $request) {
                if ($request != null) {
                    if ($request->getStatus() != $request->CLOSE) {
                        //we need to add DONE requests
                        $request->setStatus($request->CLOSE);
                        $request->setDeleted(true);
                        //to add a notification in the next sprint
                    } else {
                        $request->setDeleted(true);
                    }
                    if ($user != null) {
                        $user->removeRequest($request);
                    }
                    $offers = $request->getOffers();
                    if ($offers != null) {
                        foreach ($offers as $offer) {
                            $this->deleteOfferMessages($user, $offer);
                        }
                    }
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }
    }

    /**
     * A function that is responsible for deleting the offers
     * of a user that left the tangle
     *
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeOffers($tangleId, $userId) {
        $doctrine = $this->getDoctrine();
        $offerRepo = $doctrine->getRepository("MegasoftEntangleBundle:Offer");

        $offers = $offerRepo->findBy(array('userId' => $userId));
        if ($offers != null) {
            $requestRepo = $doctrine->getRepository("MegasoftEntangleBundle:Request");
            $userRepo = $doctrine->getRepository("MegasoftEntangleBundle:User");
            $user = $userRepo->find($userId);

            foreach ($offers as $offer) {
                if ($offer != null) {
                    $requestId = $offer->getRequestId();
                    $request = $requestRepo->findOneBy(array(
                        'id' => $requestId, 'tangleId' => $tangleId));

                    if ($request != null) {
                        $offerStatus = $offer->getStatus();
                        if ($offerStatus != $offer->DONE) {
                            $this->deleteOfferMessages($user, $offer);
                            $offer->setDeleted(true);
                            $user->removeOffer($offer);
                        }
                    }
                    //to be done in the coming sprint
                    //send notification to the requester only in 
                    //case of PENDING and ACCEPTED
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }
    }

    /**
     * This function is responsible for handling the deletion of the messages
     * related to an offer
     *
     * @param integer $user
     * @param Offer $offer
     * @author HebaAamer
     */
    private function deleteOfferMessages($user, $offer) {
        if ($user != null) {
            $messages = $offer->getMessages();
            foreach ($messages as $message) {
                if ($message != null && $message->getSenderId() == $user->getId()) {
                    $message->setDeleted(true);
                    $user->removeMessage($message);
                }
            }
        }
    }

    /**
     * A function that is responsible for removing a user from
     * a tangle and updating the deletedBalance of the tangle
     * and setting the leavingDate of that user.
     *
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeUser($tangleId, $userId) {
        $doctrine = $this->getDoctrine();
        $userTangleRepo = $doctrine->getRepository("MegasoftEntangleBundle:UserTangle");

        $userTangle = $userTangleRepo->findOneBy(array('userId' => $userId,
            'tangleId' => $tangleId));
        if ($userTangle != null) {

            $tangleRepo = $doctrine->getRepository("MegasoftEntangleBundle:Tangle");
            $tangle = $tangleRepo->find($tangleId);

            $userTangle->setLeavingDate(new DateTime('NOW'));

            if ($tangle != null) {

                $deletedBalance = $tangle->getDeletedBalance();
                $updatedDeletedBalance = $deletedBalance + ($userTangle->getCredit());
                $tangle->setDeletedBalance($updatedDeletedBalance);
            }
            $doctrine->getManager()->flush();
        }
    }

    /**
     * This function is used to remove all the claims related to specific
     *
     * @param integer $tangleId
     * @param integer $userId
     * @author HebaAamer
     */
    private function removeClaims($tangleId, $userId) {
        $claimRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Claim");
        $userRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:User");
        $user = $userRepo->find($userId);

        //to be changed to userId
        $claims = $claimRepo->findBy(array('tangleId' => $tangleId, 'usedId' => $userId));
        if ($claims != null) {
            foreach ($claims as $claim) {
                if ($claim != null) {
                    //assuming 1 resolved
                    if ($claim->getStatus() == 1) {
                        $claim->setDeleted(true);
                    }
                    if ($user != null) {
                        $user->removeClaim($claim);
                    }
                }
            }
        }
    }

    /**
     * An endpoint to be used when a user leaves a tangle
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author HebaAamer
     */
    public function leaveTangleAction(Request $request, $tangleId) {

        $verified = $this->leaveTangleVerification($request, $tangleId);
        if ($verified != null) {
            return $verified;
        }

        $sessionId = $request->headers->get("X-SESSION-ID");

        $sessionRepo = $this->getDoctrine()->getRepository("MegasoftEntangleBundle:Session");
        $session = $sessionRepo->findOneBy(array('sessionId' => $sessionId));
        $userId = $session->getUserId();

        $this->removeUser($tangleId, $userId);
        $this->removeOffers($tangleId, $userId);
        $this->removeRequests($tangleId, $userId);
        $this->removeClaims($tangleId, $userId);

        //removing notifications to be added in the coming sprint

        return new Response("You have left successfully", 204);
    }

    /**
     * The endpoint responsable for fetching the pending invitations for the tangle with id $tangleId
     * @param Request $request
     * @param integer $tangleId
     * @return JsonResponse
     * @author MohamedBassem
     */
    public function pendingInvitationsAction(Request $request, $tangleId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        $validation = $this->validateIsOwner($sessionId, $tangleId);
        if ($validation != null) {
            return $validation;
        }

        $pendingInvitationTable = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PendingInvitation');
        $pendingInvitations = $pendingInvitationTable->findBy(array('tangleId' => $tangleId, 'approved' => false));
        $responseArray = array();
        foreach ($pendingInvitations as $pendingInvitation) {
            $pending = array();
            $pending['id'] = $pendingInvitation->getId();
            if ($pendingInvitation->getInvitee() == null) {
                $pending['invitee'] = null;
            } else {
                $pending['invitee'] = $pendingInvitation->getInvitee()->getName();
            }
            $pending['inviter'] = $pendingInvitation->getInviter()->getName();
            $pending['email'] = $pendingInvitation->getEmail();
            $responseArray[] = $pending;
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setData(array('pending-invitations' => $responseArray));
        return $jsonResponse;
    }

    /**
     * An endpoint to accept the pending invitation with id $pendingInvitationId and sends the
     * invitation email to the user
     * @param Request $request
     * @param integer $pendingInvitationId
     * @return Response
     * @author MohamedBassem
     */
    public function acceptPendingInvitationAction(Request $request, $pendingInvitationId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        $pendingInvitationTable = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PendingInvitation');
        $pendingInvitation = $pendingInvitationTable->findOneBy(array('id' => $pendingInvitationId));

        if ($pendingInvitation == null) {
            return new Response("Pending Invitation Not Found", 404);
        }

        if ($pendingInvitation->getApproved()) {
            return new Response("Bad Request", 400);
        }

        $validation = $this->validateIsOwner($sessionId, $pendingInvitation->getTangleId());

        if ($validation != null) {
            return $validation;
        }


        $message = $pendingInvitation->getMessage()->getBody();
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        $email = $pendingInvitation->getEmail();

        if ($this->isNewMember($email) || !$this->isTangleMember($email, $pendingInvitation->getTangleId()) ) {
            $this->inviteuser($email,$pendingInvitation->getTangleId(),$pendingInvitation->getInviterId(),$message);
            $pendingInvitation->setApproved(true);
            $this->getDoctrine()->getManager()->flush();
            return new Response("Approved", 200);
        } else {
            $pendingInvitation->setApproved(true);
            $this->getDoctrine()->getManager()->flush();
            return new Response("Already in the tangle", 200);
        }
    }

    /**
     * An endpoint to reject the pending invitation with id $pendingInvitationId
     * @param Request $request
     * @param integer $pendingInvitationId
     * @return Response
     * @author MohamedBassem
     */
    public function rejectPendingInvitationAction(Request $request, $pendingInvitationId) {
        $sessionId = $request->headers->get('X-SESSION-ID');

        $pendingInvitationTable = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:PendingInvitation');
        $pendingInvitation = $pendingInvitationTable->findOneBy(array('id' => $pendingInvitationId));

        if ($pendingInvitation == null) {
            return new Response("Pending Invitation Not Found", 404);
        }

        if ($pendingInvitation->getApproved()) {
            return new Response("Bad Request", 400);
        }

        $validation = $this->validateIsOwner($sessionId, $pendingInvitation->getTangleId());

        if ($validation != null) {
            return $validation;
        }

        $this->getDoctrine()->getManager()->remove($pendingInvitation);
        $this->getDoctrine()->getManager()->flush();
        return new Response("Deleted", 200);
    }

    /**
     * An endpoint for accepting tangle invitations sent to user
     * @param int invitationID
     * @return Response
     * @author MahmoudGamal
     */
    public function acceptInvitationAction($invitationCode) {
        $criteria1 = array('Code' => $invitationCode);
        $invitation = $this->getDoctrine()
                ->getRepository('MegasoftEntangleBundle:InvitationCode')
                ->findBy($criteria1);
        if (!$invitation) {
            return new Response("Invitation not found", 404);
        }
        $expired = $invitation->getExpired();
        if ($expired) {
            return new Response("Invitation expired", 400);
        }
        $tangleId = $invitation->getTangleId();
        $userId = $invitation->getUserId();
        $tangle = $this->getDoctrine()
            ->getRepository('MegasoftEntangleBundle:Tangle')
            ->find($tangleId);
        if (!$tangle) {
            return new Response("Tangle not found", 404);
        }
        $user = $this->getDoctrine()
            ->getRepository('MegasoftEntangleBundle:User')
            ->find($userId);
        if (!$user) {
            return new Response("User not found", 404);
        }
        $criteria2 = array('userId' => $userId, 'tangleId' => $tangleId);

        $search = current($this->getDoctrine()
                        ->getRepository('MegasoftEntangleBundle:UserTangle')
                        ->findBy($criteria2));
        if ($search) {
            return new Response("User already exists in tangle", 400);
        }
        $tangleUser = new UserTangle();
        $tangleUser->setTangleOwner(FALSE);
        $tangleUser->setUser($user);
        $tangleUser->setTangle($tangle);
        $tangleUser->setCredit(0);
        $this->getDoctrine()->getManager()->flush();
        return new Response("User added", 201);
    }

    /**
     * The endpoint resposible for fetching the tangles of a certain user from the database
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @author MohamedBassem
     */
    public function getTanglesAction(Request $request) {
        $sessionId = $request->headers->get('X-SESSION-ID');
        $doctrine = $this->getDoctrine();

        if ($sessionId == null) {
            return new Response("Bad Request", 400);
        }

        $sesionRepo = $doctrine->getRepository('MegasoftEntangleBundle:Session');

        $session = $sesionRepo->findOneBy(array('sessionId' => $sessionId));

        if ($session == null || $session->getExpired()) {
            return new Response("Unauthorized", 401);
        }


        $userId = $session->getUserId();
        $UserTanglerepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $tangles = $UserTanglerepo->findBy(array('userId' => $userId, 'leavingDate' => null));
        $ret = array();
        foreach ($tangles as $tangle) {
            $ret[] = array("id" => $tangle->getTangleId(), "name" => $tangle->getTangle()->getName());
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setData(array("tangles" => $ret));
        return $jsonResponse;
    }

    /**
     * An endpoint to return the list of users in a specific tangle
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $tangleId
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Omar ElAzazy
     */
    public function allUsersAction(\Symfony\Component\HttpFoundation\Request $request, $tangleId) {
        $verification = $this->verifyUser($request, $tangleId);

        if ($verification != null) {
            return $verification;
        }

        $doctrine = $this->getDoctrine();
        $userTangleRepo = $doctrine->getRepository('MegasoftEntangleBundle:UserTangle');
        $userTangles = $userTangleRepo->findBy(array('tangleId' => $tangleId));

        $usersJsonArray = array();

        foreach ($userTangles as $userTangle) {
            $usersJsonArray[] = array(
                'id' => $userTangle->getUserId(),
                'username' => $userTangle->getUser()->getName(),
                'balance' => $userTangle->getCredit(),
                'iconUrl' => 'http://entangle.io/images/profilePictures/' . $userTangle->getUser()->getPhoto()
            );
        }

        $response = new JsonResponse();
        $response->setData(array('count' => sizeof($usersJsonArray), 'users' => $usersJsonArray));

        return $response;
    }

}