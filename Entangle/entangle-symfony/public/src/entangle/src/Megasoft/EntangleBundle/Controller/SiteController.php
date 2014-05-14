<?php

namespace Megasoft\EntangleBundle\Controller;


use Megasoft\EntangleBundle\Entity\InvitationMessage;
use Megasoft\EntangleBundle\Entity\PendingInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Exception\LogicException;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;


class SiteController extends Controller
{

    public function indexAction()
    {
        return $this->render('MegasoftEntangleBundle:Site:index.html.twig');
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
            $email = $request->get('your-email');
            $userBio = $request->get('userBio');

            $user = new User;
            $userEmail = new UserEmail();
            $user->addEmail($userEmail);
            if (($this->validateUniqueUsername($name) && ($this->EmailIsUnique($email)))) {
                $user->setName($name);
                $user->setPassword($password);
                $userEmail->setEmail($email);
                $user->setUserBio($userBio);
                //$image = $request->files->get('img');
//                if (($image instanceof UploadedFile) && ($image->getError() == '0')) {
//                    if ($image->getSize() < 4194304) { //if image size is less that 4MB
//                        $originalName = $image->getClientOriginalName();
//                        $nameArray = explode('.', $originalName);
//
//                        $fileType = $nameArray[sizeof($nameArray) - 1];
//                        $validFileTypes = array('jpg', 'jpeg', 'bmp',
//                            'png');
//
//                        if (in_array(strtolower($fileType), $validFileTypes)) {
//                            $kernel = $this->get('kernel');
//                            $filename = substr(md5(time()), 0, 10) . $this->generate(5);
//                            $filepath = $kernel->getRootDir() . '/../web/images/profilePictures/' . $filename . '.' . $fileType;
//                            move_uploaded_file($image, $filepath);
//                            $user->setPhoto($filename . '.' .$fileType);
//                        }
//                    }
//                }
                $entityManager = $this->getDoctrine()->getEntityManager();
                $entityManager->persist($user);
                $entityManager->persist($userEmail);
                $entityManager->flush();
                $status = "Successful!";
                $message = "Account created sweetie!";

                return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));

            } else if (!($this->validateUniqueUsername($name))) {
                $status = "Failed!";
                $message = "Username is not unique!";
                return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));

            } else if (!($this->EmailIsUnique($email))) {
                $status = "Failed!";
                $message = "Email is not unique!";
                return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));

            }
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));
        }
        return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => $status, 'message' => $message));
    }

    private function generateSessionId($len)
    {
        $generatedSessionID = '';
        $seed = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        for ($i = 0; $i < $len; $i++) {
            $generatedSessionID .= $seed[rand(0, strlen($seed) - 1)];
        }
        return $generatedSessionID;
    }

    public function loginAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        if ($request->getMethod() != 'POST')
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => 400, 'message' => 'wrong request type'));

        $name = $request->get('name');
        $password = $request->get('password');
        if (!$name) {
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => 400, 'message' => 'Missing user name'));

        }
        if (!$password) {
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => 400, 'message' => 'Missing password'));

        }
        if (strstr("\"", $name) || strstr("'", $name)) {
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => 400, 'message' => 'The username shall not have special characters'));
        }
        $sessionId = $this->generateSessionId(30);

        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:User');
        $user = $repo->findOneBy(array('name' => $name, 'password' => $password));
        if (!$user) {
            return $this->render('MegasoftEntangleBundle:Site:index.html.twig', array('status' => 400, 'message' => 'Bad Request'));

        }
        $repo2 = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        $userTangles = $user->getTangles();
        $tanglesToBeAdded = array();


        foreach ($userTangles as $tangle) {

            $tangleOwner = $repo2->findOneBy(array('tangleId' => $tangle->getId(), 'userId' => $user->getId(), 'tangleOwner' => 0,));
            if (!$tangleOwner) {
                array_push($tanglesToBeAdded, $tangle->getId());
                $tangleOwner = null;
            }
        }


        $session = new Session();
        $session->setSessionId($sessionId);
        $session->setUser($user);
        $session->setUserId($user->getId());
        $session->setCreated(new \DateTime('now'));
        $session->setExpired(0);
        $session->setRegId("ToAvoidNull");
        $session->setDeviceType("Galaxy S3");

        $user->addSession($session);

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->persist($session);

        $this->getDoctrine()->getManager()->flush();

//        return $this->render('MegasoftEntangleBundle:Site:manageTangle.html.twig', array('sessionId' => $sessionId,));
        return $this->manageTangleAction($sessionId);
    }

    public function manageTangleAction( $sessionId)
    {
//        $sessionId = $request->get('sessionId');
        $repo = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:Session');
        $session = $repo->findOneBy(array('sessionId' => $sessionId,));
        if (!$session) {
            return new Response(400, "adeek fel 2olla tfa77ar");
        }
        $user = $session->getUser();
        if (!$user)
            return new Response(400, 'bad request');
        $repo2 = $this->getDoctrine()->getRepository('MegasoftEntangleBundle:UserTangle');

        $userTangles = $user->getTangles();
        $tanglesToBeAdded = array();


        foreach ($userTangles as $tangle) {

            $tangleOwner = $repo2->findOneBy(array('tangleId' => $tangle->getId(), 'userId' => $user->getId(), 'tangleOwner' => 1,));
            if ($tangleOwner) {
                array_push($tanglesToBeAdded, $tangle);
            }
        }
        if (sizeof($tanglesToBeAdded) > 0) {
            return $this->render('MegasoftEntangleBundle:Site:selectTangle.html.twig', array('tangles' => $tanglesToBeAdded,'sessionId'=> $sessionId,));
        }

        return $this->render('MegasoftEntangleBundle:Site:createTangle.html.twig' , array('sessionId'=> $sessionId,));


    }
}

