<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class User extends Controller {

    public function edit(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        Megasoft\EntangleBundle\Entity\User:: $user = $jsonArray['user'];
        $newDescription = $jsonArray['newDescription'];
        if (strcmp($user->getUserBio(), $newDescription) != 0) {
            $user->setUserBio($newDescription);
        }
        $newPassword = $jsonArray['newPassword'];
        $confirmNewPassword = $jsonArray['confirmNewPassword'];
        $givenCurrentPassword = $jsonArray['givenCurrentPassword'];
        $currentPassword = $user->getPassword();
        if ($givenCurrentPassword == $currentPassword && $newPassword == $confirmNewPassword) {
            $user->setPassword($newPassword);
        }
        $newDateOfBirth = $jsonArray['newDateOfBirth'];
        if (strcmp($newDateOfBirth, $user->getBirthDate()) != 0) {
            $user->setBirthDate($newDateOfBirth);
        }
        $newMail = new UserEmail();
        $newMail->setEmail($jsonArray['newMail']);
        $newMail->setUser($user);
        $user->addEmail($newMail);
    }

    public function deleteSecondaryEmail(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $deletedMail = $jsonArray['deletedMail'];
        $user->removeEmail($deletedMail);
    }

}
