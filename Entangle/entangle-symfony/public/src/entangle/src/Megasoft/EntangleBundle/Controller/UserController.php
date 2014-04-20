<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\UserEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    public function edit(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $sessionId = $jsonArray['session_id'];
        $sesionRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $currentSession = $sesionRepo->findOneBy(array('sessionId' => $sessionId));
        $user = $currentSession->getUser();
        $newDescription = $jsonArray['description'];
        if (strcmp($user->getUserBio(), $newDescription) != 0) {
            $user->setUserBio($newDescription);
        }
        $newPassword = $jsonArray['new_password'];
        $confirmNewPassword = $jsonArray['confirm_password'];
        $givenCurrentPassword = $jsonArray['current_password'];
        $currentPassword = $user->getPassword();
        if ($givenCurrentPassword == $currentPassword && $newPassword == $confirmNewPassword) {
            $user->setPassword(md5($newPassword));
        }
        $newDateOfBirth = $jsonArray['newDateOfBirth'];
        $dateGiven = date($user->getBirthDate());
        $dateString = $dateGiven->format("m-d-Y");
        if (strcmp($newDateOfBirth, $dateString) != 0) {
            $user->setBirthDate($newDateOfBirth);
        }
        $doctrineManger = $this->getDoctrine()->getManager();

        if (filter_var($jsonArray['added_mail'], FILTER_VALIDATE_EMAIL)) {
            $newMail = new UserEmail();
            $newMail->setEmail($jsonArray['added_mail']);
            $newMail->setUser($user);
            $user->addEmail($newMail);
            $doctrineManger->persist($newMail);
        }
        $doctrineManger->persist($user);
        $doctrineManger->flush();
    }

    public function deleteSecondaryEmail(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $deletedMail = $jsonArray['deletedMail'];
        $user->removeEmail($deletedMail);
        $doctrineManger = $this->getDoctrine()->getManager();
        $doctrineManger->persist($user);
        $doctrineManger->flush();
    }

}
