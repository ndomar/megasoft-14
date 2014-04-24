<?php

namespace Megasoft\EntangleBundle\Controller;

use Megasoft\EntangleBundle\Entity\InvitationMessage;
use Megasoft\EntangleBundle\Entity\PendingInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Exception\LogicException;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;

/**
 * Class RegisterController
 * @package Megasoft\EntangleBundle\Controller
 * Responsible for the user creation process
 * @author: Eslam
 */
class RegisterController extends Controller {

    public function indexAction($name) {
        return $this->render('MegasoftEntangleBundle:Register:register.html.twig', array('name' => $name));
    }

    /**
     * generates a random string
     * @param string $len
     * @return String $ret
     * @author: Eslam
     * * */
    private function generate($len) {
        $ret = '';
        $seed = "abcdefghijklmnopqrstuvwxyz123456789";
        for ($i = 0; $i < $len; $i++) {
            $ret .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $ret;
    }

    /**
     * checks if this username is unique
     * @param string $username
     * @return boolean true if the username is unique , false otherwise
     * @author: Eslam
     * * */
    private function validateUniqueUsername($username) {
        $nameRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        if ($nameRepo->findOneBy(array('name' => $username)) == null && $username != null && $username != "") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks that both passwords match.
     * @param String password, String confirm password
     * @return Boolean value, true if they match. False otherwise.
     * @author: Eslam
     * * */
    private function passwordsMatch($password, $confirmPassword) {
        if ($password == $confirmPassword) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks that the email provided is unique
     * @param String email
     * @return Boolean value, true if unique. false otherwise
     * @author: Eslam
     * * */
    private function EmailIsUnique($email) {
        $emailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        if ($emailRepo->findOneBy(array('email' =>$email))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Creates a new user once all required fields are filled
     * @param Request $request
     * @return void (Creates a new user if successful, otherwise it returns an error)
     * @author: Eslam
     * * */
    public function registerAction(\Symfony\Component\HttpFoundation\Request $request) {
        if ($request->getMethod() == 'POST') {
            $name = $request->get('name');
            $password = $request->get('password');
            $confirmPassword = $request->get('confirmPassword');
            $email = $request->get('email');
            $userBio = $request->get('userBio');
            $birthDate = new \DateTime($request->get('birthDate'));
            $user = new User;
            $userEmail = new UserEmail();
            $user->addEmail($userEmail);
            if ($this->validateUniqueUsername($name) && ($this->passwordsMatch($password, $confirmPassword) && ($this->EmailIsUnique($email)))) {
                $user->setName($name);
                $user->setPassword($password);
                $userEmail->setEmail($email);
            }
            $user->setUserBio($userBio);

            if ($birthDate != null && $birthDate != "") {
                $user->setBirthDate($birthDate);
            }

            $user->setVerified(FALSE);


            $image = $request->files->get('img');
            if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
                if ($image->getSize() < 4194304) { //if image size is less that 4MB
                    $originalName = $image->getClientOriginalName();
                    $nameArray = explode('.', $originalName);
                    $fileType = $nameArray[sizeof($nameArray) - 1];
                    $validFileTypes = array('jpg', 'jpeg', 'bmp',
                        'png');

                    if (in_array(strtolower($fileType), $validFileTypes)) {

                        $filepath = '/home/neuron/Documents/megasoft-14/Entangle/entangle-symfony/public/src'
                                . '/entangle/web/images/profilePictures/' . substr(md5(time()), 0, 10) .
                                $this->generate(5) . '.' . $fileType;
                        move_uploaded_file($image, $filepath);
                        $user->setPhoto($filepath);
                    }
                }
            }
            $entityManager = $this->getDoctrine()->getEntityManager();
            $entityManager->persist($user);
            $entityManager->persist($userEmail);
            $entityManager->flush();
            return new Response("Created", 201);
        }
        return $this->render('MegasoftEntangleBundle:Register:register.html.twig');
    }

}
