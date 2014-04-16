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

    /**
     * This method edits the user's Description
     * @param \Symfony\Component\HttpFoundation\Request $r
     */
    public function editDescription(Request $r) {

        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        Megasoft\EntangleBundle\Entity\User:: $user = $jsonArray['user'];
        $newDescription = $jsonArray['newDescription'];
        $user->setUserBio($newDescription);
    }

    /**
     * This method edits the user's Date of Birth
     * @param \Symfony\Component\HttpFoundation\Request $r
     */
    public function editDateOfBirth(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $newDateOfBirth = $jsonArray['newDateOfBirth'];
        $user->setBirthDate($newDateOfBirth);
    }

    /**
     * This method Changes the user's password
     * @param \Symfony\Component\HttpFoundation\Request $r
     */
    public function editPassword(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $newPassword = $jsonArray['newPassword'];
        $confirmNewPassword = $jsonArray['confirmNewPassword'];
        $givenCurrentPassword = $jsonArray['givenCurrentPassword'];
        $currentPassword = $user->getPassword();
        if ($givenCurrentPassword == $currentPassword && $newPassword == $confirmNewPassword) {
            $user->setPassword($newPassword);
        }
    }

    /**
     * This Method adds a secondary mail
     * @param \Symfony\Component\HttpFoundation\Request $r
     */
    public function addMail(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $newMail = new UserEmail();
              $newMail->setEmail($jsonArray['newMail']);
              $newMail->setUser($jsonArray['user']);
        $user->addEmail($newMail);
    }

    /**
     * This method deletes a secondary mail
     * @param \Symfony\Component\HttpFoundation\Request $r
     */
    public function deleteSecondaryEmail(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $deletedMail = $jsonArray['deletedMail'];
        $user->removeEmail($deletedMail);
    }

}
