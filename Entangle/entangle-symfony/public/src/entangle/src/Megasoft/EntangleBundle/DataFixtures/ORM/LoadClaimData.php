<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Claim;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Offer;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Session;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LoadClaimData
 *
 * @author sak
 */
class LoadClaimData extends AbstractFixture {
    public function load(ObjectManager $manager) {
        $user1 = new User(); 
        $user1->setName('user1'); 
        $user1->setPassword('password'); 
        $manager->persist($user1);
        
        $session1 = new Session();
        $session1->setUser($user1);
        $session1->setSessionId('sampleSession');
        $session1->setExpired(false);
        $session1->setCreated(new DateTime('now'));
        $session1->setDeviceType('Microsoft Surface Pro');
        $session1->setRegId(1);
        $manager->persist($session1);
       
        $user2 = new User(); 
        $user2->setName('user2'); 
        $user2->setPassword('password');
        $manager->persist($user2);
        
        $session2 = new Session();
        $session2->setUser($user2);
        $session2->setSessionId('sampleSession2');
        $session2->setExpired(false);
        $session2->setCreated(new DateTime('now'));
        $session2->setDeviceType('Microsoft Surface Pro');
        $session2->setRegId(1);
        $manager->persist($session2);
        
        
        $user3 = new User(); 
        $user3->setName('user3'); 
        $user3->setPassword('password'); 
        $manager->persist($user3);
        
        $session3 = new Session();
        $session3->setUser($user3);
        $session3->setSessionId('sampleSession3');
        $session3->setExpired(false);
        $session3->setCreated(new DateTime('now'));
        $session3->setDeviceType('Microsoft Surface Pro');
        $session3->setRegId(1);
        $manager->persist($session3);
        
        $tangle = new Tangle();
        $tangle->setName('tangle');
        $tangle->setDeletedBalance(0); 	
        $tangle->setDescription("description");
        $manager->persist($tangle);
        
        
        $tangle1= new UserTangle();
        $tangle1->setUser($user1);
        $tangle1->setTangle($tangle);
        $tangle1->setTangleOwner(1);
        $tangle1->setCredit(100);
        $manager->persist($tangle1);
        
        
        $tangle2= new UserTangle();
        $tangle2->setUser($user2);
        $tangle2->setTangle($tangle);
        $tangle2->setTangleOwner(0);
        $tangle2->setCredit(100);
        $manager->persist($tangle2);
        
        $tangle3= new UserTangle();
        $tangle3->setUser($user3);
        $tangle3->setTangle($tangle);
        $tangle3->setTangleOwner(1);
        $tangle3->setCredit(100);
        $manager->persist($tangle3);
        
        
        $request = new Request(); 
        $request->setStatus(0);
        $request->setDescription('test'); 
        $request->setDate(new DateTime('now')); 
        $request->setTangle($tangle);
        $request->setUser($user1);
        $manager->persist($request);
        
        $offer= new Offer();
        $offer->setRequestedPrice(10);
        $offer->setDate(new DateTime('now'));
        $offer->setDescription('test');
        $offer->setStatus(0);
        $offer->setUser($user2);
        $offer->setRequest($request);
        
        $manager->persist($offer);
        
        
        $claim = new Claim();
        $claim->setStatus(0);
        $claim->setMessage('message');
        $claim->setClaimer($user1);
        $claim->setTangle($tangle);
        $claim->setDeleted(0);
        $claim->setCreated(new DateTime('now'));
        $claim->setOffer($offer); 
        $manager->persist($claim);
        $this->addReference('sampleClaim',$claim);
        $this->addReference('sampleSession', $session1);
        $this->addReference('sampleSession2', $session2);
        $this->addReference('sampleSession3', $session3);
        $manager->flush();
    }
}
