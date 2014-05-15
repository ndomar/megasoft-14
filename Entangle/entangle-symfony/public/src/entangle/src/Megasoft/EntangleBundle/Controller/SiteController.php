<?php

namespace Megasoft\EntangleBundle\Controller;


use Megasoft\EntangleBundle\Entity\InvitationMessage;
use Megasoft\EntangleBundle\Entity\PendingInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Exception\LogicException;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;

class SiteController extends Controller
{

    public function indexAction()
    {
        return $this->render('MegasoftEntangleBundle:Register:register.html.twig');
    }


    /**
     * generates a random string
     * @param string $len
     * @return String $ret
     * @author: Eslam
     * * */
    private function generate($len)
    {
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
    private function validateUniqueUsername($username)
    {
        $nameRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        if ($nameRepo->findOneBy(array('name' => $username)) == null && $username != null && $username != "") {
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
    private function EmailIsUnique($email)
    {
        $emailRepo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserEmail');
        if ($emailRepo->findOneBy(array('email' => $email))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * checks that the passwords provided when registering match
     * @param: String password
     * @param: String confirm Password
     * @return: Boolean value, true if they match, false otherwise
     * @author: Eslam
     */

    private function checkMatchingPasswords($password, $confirmPassword)
    {
        if ($password == $confirmPassword) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Creates a new user once all required fields are filled
     * @param Request $request
     * @return void (Creates a new user if successful, otherwise it returns an error)
     * @author: Eslam
     * * */
    public function registerAction(\Symfony\Component\HttpFoundation\Request $request)
    {

        $status = "";
        $message = "";

        if ($request->getMethod() == 'POST') {

            $name = $request->get('your-name');
            $password = $request->get('password');
            $cpassword = $request->get('cp');
            $email = $request->get('your-email');
            $userBio = $request->get('userBio');


            if ($this->validateUniqueUsername($name) && ($this->EmailIsUnique($email)) && ($this->checkMatchingPasswords($password, $cpassword))) {
                $user = new User;
                $userEmail = new UserEmail();
                $user->addEmail($userEmail);
                $user->setName($name);
                $user->setPassword($password);
                $userEmail->setEmail($email);
                $user->setUserBio($userBio);
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->persist($user);
                $entityManager->persist($userEmail);
                $entityManager->flush();
                $status = "Successful!";
                $message = "Account created";

                return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));

            } else if (!($this->validateUniqueUsername($name))) {
                $status = "Failed!";
                $message = "Username is not unique!";
                return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));

            } else if (!($this->EmailIsUnique($email))) {
                $status = "Failed!";
                $message = "Email is not unique!";
                return $this->render('MegasoftEntangleBundle:Site:index$confirmPassword.html.twig', array('status' => $status, 'message' => $message));

            } else if (!($this->checkMatchingPasswords($password, $cpassword))) {
                $status = "Failed!";
                $message = "Passwords doesn't match";
                return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));

            }
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));
        }
        return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));
    }

}

