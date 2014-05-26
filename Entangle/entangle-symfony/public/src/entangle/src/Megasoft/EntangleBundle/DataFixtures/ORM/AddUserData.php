<?php
/**
 * Created by PhpStorm.
 * User: neuron
 * Date: 5/16/14
 * Time: 5:00 PM
 */

namespace Megasoft\EntangleBundle\DataFixtures\ORM;


use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserEmail;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AddUserData extends AbstractFixture implements OrderedFixtureInterface {

    public function load (ObjectManager $manager) {
        $user = new User();
        $userEmail = new UserEmail();
        $user->setName("Zanaty");
        $userEmail->setEmail("zanaty@gmail.com");
        $user->setPassword('1234567890');
        $this->addReference('Zanaty', $user);
        $this->addReference('ZanatyMail', $userEmail);
        $manager->persist($user);
        $manager->flush();



    }

    public function getOrder()
    {
        return 1;
    }
}