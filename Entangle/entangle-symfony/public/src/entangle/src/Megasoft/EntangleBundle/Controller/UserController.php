s<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\UserEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller {

    public function editAction(Request $request) {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        $user = $currentSession->getUser();
        $newDescription = $jsonArray['description'];
        if ($user->getUserBio() != $newDescription) {
            $user->setUserBio($newDescription);
        }
        $newPassword = $jsonArray['new_password'];
        $confirmNewPassword = $jsonArray['confirm_password'];
        $givenCurrentPassword = $jsonArray['current_password'];
        $currentPassword = $user->getPassword();
        if (($givenCurrentPassword == $currentPassword) && ($newPassword == $confirmNewPassword)) {
            $user->setPassword(md5($newPassword));
        }
        $newDateOfBirthString = $jsonArray['date_of_birth'];
        $newDateOfBirth = strtotime($newDateOfBirthString);
        $dateGiven = $user->getBirthDate();

        if ($newDateOfBirth != $dateGiven) {
            $user->setBirthDate($newDateOfBirth);
        }
        $doctrineManger = $this->getDoctrine()->getManager();

        if (filter_var($jsonArray['added_email'], FILTER_VALIDATE_EMAIL)) {
            $newMail = new UserEmail();
            $newMail->setEmail($jsonArray['added_email']);
            $newMail->setUser($user);
            $newMail->setUserId($user->getId());
            $user->addEmail($newMail);
            $doctrineManger->persist($newMail);
        }
        $doctrineManger->persist($user);
        $doctrineManger->flush();
        return new Response('OK', 200);
    }

    public function deleteSecondaryEmailAction(Request $request) {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        $user = $currentSession->getUser();
        $deletedMail = $jsonArray['deletedMail'];
        $user->removeEmail($deletedMail);
        $doctrineManger = $this->getDoctrine()->getManager();
        $doctrineManger->persist($user);
        $doctrineManger->flush();
        return new Response('OK', 200);
    }

    public function retrieveDataAction(Request $request) {
        $requestContent = $request->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $request->headers->get('X-SESSION-ID');
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        $user = $currentSession->getUser();
    }

}
