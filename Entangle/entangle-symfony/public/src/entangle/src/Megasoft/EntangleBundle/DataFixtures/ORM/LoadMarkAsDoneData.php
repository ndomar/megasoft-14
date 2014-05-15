<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\UserTangle;



/**
* Fixtures for Offer table
*
* @author mohamedzayan
*/
class LoadMarkAsDoneData extends AbstractFixture {

    /**
* {@inheritDoc}
*/
    public function load(ObjectManager $manager) {
        $user = new User();
        $user->setName('sampleUser');
        $user->setPassword('samplePassword');
        $manager->persist($user);
        $manager->flush();
        $this->addReference('sampleUser', $user);

        $user2 = new User();
        $user2->setName('sampleUser2');
        $user2->setPassword('samplePassword');
        $manager->persist($user2);
        $manager->flush();
        $this->addReference('sampleUser2', $user2);
        $user3 = new User();
        $user3->setName('sampleUser3');
        $user3->setPassword('samplePassword');
        $manager->persist($user3);
        $manager->flush();
        $this->addReference('sampleUser3', $user3);

        $user4 = new User();
        $user4->setName('sampleUser4');
        $user4->setPassword('samplePassword');
        $manager->persist($user4);
        $manager->flush();
        $this->addReference('sampleUser4', $user4);

        $user5 = new User();
        $user5->setName('sampleUser5');
        $user5->setPassword('samplePassword');
        $manager->persist($user5);
        $manager->flush();
        $this->addReference('sampleUser5', $user5);

        $user6 = new User();
        $user6->setName('sampleUser6');
        $user6->setPassword('samplePassword');
        $manager->persist($user6);
        $manager->flush();
        $this->addReference('sampleUser6', $user6);

        $user7 = new User();
        $user7->setName('sampleUser7');
        $user7->setPassword('samplePassword');
        $manager->persist($user7);
        $manager->flush();
        $this->addReference('sampleUser7', $user7);
       
        $session = new Session();
        $session->setUser($this->getReference('sampleUser'));
        $session->setSessionId('sampleSession');
        $session->setExpired(false);
        $session->setCreated(new \DateTime('now'));
        $session->setDeviceType('Microsoft Surface Pro');
        $session->setRegId(1);
        $manager->persist($session);
        $manager->flush();
        $this->addReference('sampleSession', $session);
        $session2 = new Session();
        $session2->setUser($this->getReference('sampleUser2'));
        $session2->setSessionId('sampleSession2');
        $session2->setExpired(false);
        $session2->setCreated(new \DateTime('now'));
        $session2->setDeviceType('Microsoft Surface Pro');
        $session2->setRegId(1);
        $manager->persist($session2);
        $manager->flush();
        $this->addReference('sampleSession2', $session2);
        $tangle = new Tangle();
        $tangle->setName('sampleTangle');
        $tangle->setDescription('Just a sample tangle');
        
        $manager->persist($tangle);
        $manager->flush();
        
        $this->addReference('sampleTangle', $tangle);
        $Request = new Request();
        $Request->setRequestedPrice(15);
        $Request->setDescription('Just a sample request');
        $Request->setStatus(2);
        $Request->setDate(new \DateTime('now'));
        $Request->setUser($this->getReference('sampleUser'));
        $Request->setTangle($tangle);
        $Request->setDeleted(false);
        $manager->persist($Request);
        $manager->flush();
        $this->addReference('sampleRequest', $Request);

        $Request2 = new Request();
        $Request2->setRequestedPrice(15);
        $Request2->setDescription('Just a sample request');
        $Request2->setStatus(0);
        $Request2->setDate(new \DateTime('now'));
        $Request2->setUser($this->getReference('sampleUser'));
        $Request2->setTangle($tangle);
        $Request2->setDeleted(true);
        $manager->persist($Request2);
        $manager->flush();
        $this->addReference('sampleRequest2', $Request2);

        $Request3 = new Request();
        $Request3->setRequestedPrice(15);
        $Request3->setDescription('Just a sample request');
        $Request3->setStatus(1);
        $Request3->setDate(new \DateTime('now'));
        $Request3->setUser($this->getReference('sampleUser'));
        $Request3->setTangle($tangle);
        $Request3->setDeleted(false);
        $manager->persist($Request3);
        $manager->flush();
        $this->addReference('sampleRequest3', $Request3);
        
        $Request4 = new Request();
        $Request4->setRequestedPrice(15);
        $Request4->setDescription('Just a sample request');
        $Request4->setStatus(2);
        $Request4->setDate(new \DateTime('now'));
        $Request4->setUser($this->getReference('sampleUser'));
        $Request4->setTangle($tangle);
        $Request4->setDeleted(false);
        $manager->persist($Request4);
        $manager->flush();
        $this->addReference('sampleRequest4', $Request4);

        $offer = new Offer();
        $offer->setRequestedPrice(15);
        $offer->setDescription('Just a sample offer');
        $offer->setStatus(2);
        $offer->setUser($this->getReference('sampleUser2'));
        $offer->setRequest($this->getReference('sampleRequest'));
        $offer->setDate(new \DateTime('now'));
        $offer->setDeleted(false);
        $manager->persist($offer);
        $manager->flush();

        $offer2 = new Offer();
        $offer2->setRequestedPrice(15);
        $offer2->setDescription('Just a sample offer');
        $offer2->setStatus(2);
        $offer2->setUser($this->getReference('sampleUser2'));
        $offer2->setRequest($this->getReference('sampleRequest2'));
        $offer2->setDate(new \DateTime('now'));
        $offer2->setDeleted(false);
        $manager->persist($offer2);
        $manager->flush();

        $offer3 = new Offer();
        $offer3->setRequestedPrice(15);
        $offer3->setDescription('Just a sample offer');
        $offer3->setStatus(2);
        $offer3->setUser($this->getReference('sampleUser2'));
        $offer3->setRequest($this->getReference('sampleRequest3'));
        $offer3->setDate(new \DateTime('now'));
        $offer3->setDeleted(false);
        $manager->persist($offer3);
        $manager->flush();

        $offer4 = new Offer();
        $offer4->setRequestedPrice(15);
        $offer4->setDescription('Just a sample offer');
        $offer4->setStatus(1);
        $offer4->setUser($this->getReference('sampleUser3'));
        $offer4->setRequest($this->getReference('sampleRequest4'));
        $offer4->setDate(new \DateTime('now'));
        $offer4->setDeleted(false);
        $manager->persist($offer4);
        $manager->flush();

        $offer5 = new Offer();
        $offer5->setRequestedPrice(15);
        $offer5->setDescription('Just a sample offer');
        $offer5->setStatus(0);
        $offer5->setUser($this->getReference('sampleUser4'));
        $offer5->setRequest($this->getReference('sampleRequest4'));
        $offer5->setDate(new \DateTime('now'));
        $offer5->setDeleted(false);
        $manager->persist($offer5);
        $manager->flush();

        $offer6 = new Offer();
        $offer6->setRequestedPrice(15);
        $offer6->setDescription('Just a sample offer');
        $offer6->setStatus(3);
        $offer6->setUser($this->getReference('sampleUser5'));
        $offer6->setRequest($this->getReference('sampleRequest4'));
        $offer6->setDate(new \DateTime('now'));
        $offer6->setDeleted(false);
        $manager->persist($offer6);
        $manager->flush();

        $offer7 = new Offer();
        $offer7->setRequestedPrice(15);
        $offer7->setDescription('Just a sample offer');
        $offer7->setStatus(4);
        $offer7->setUser($this->getReference('sampleUser6'));
        $offer7->setRequest($this->getReference('sampleRequest4'));
        $offer7->setDate(new \DateTime('now'));
        $offer7->setDeleted(false);
        $manager->persist($offer7);
        $manager->flush();

        $offer8 = new Offer();
        $offer8->setRequestedPrice(15);
        $offer8->setDescription('Just a sample offer');
        $offer8->setStatus(2);
        $offer8->setUser($this->getReference('sampleUser7'));
        $offer8->setRequest($this->getReference('sampleRequest3'));
        $offer8->setDate(new \DateTime('now'));
        $offer8->setDeleted(true);
        $manager->persist($offer8);
        $manager->flush();
        
        $userTangle = new UserTangle();
        $userTangle->setCredit(0);
        $userTangle->setTangle($tangle);
        $userTangle->setUser($this->getReference('sampleUser2'));
        $userTangle->setTangleOwner(true);
        
        $manager->persist($userTangle);
        $manager->flush();
       
        $this->addReference('sampleUserTangle', $userTangle);
    }

}