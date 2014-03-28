<?php

namespace Megasoft\EntangleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class User extends Controller {

    public function EditDescription(Request $r) {

        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $newDescription = $jsonArray['newDescription'];
        $user->setUserBio($newDescription);
    }

    public function EditDateOfBirth(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $newDateOfBirth = $jsonArray['newDateOfBirth'];
        $user->setBirthDate($newDateOfBirth);
    }

    public function EditPassword(Request $r) {
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

    public function AddMail(Request $r) {
        $requestContent = $r->getContent();
        $jsonArray = json_decode($requestContent, true);
        $user = $jsonArray['user'];
        $newMail = $jsonArray['newMail'];
        $currentEmails = $user->getEmails();
        array_push($currentEmails, $newMail);
        $user->addEmail($currentEmails);
    }

}
