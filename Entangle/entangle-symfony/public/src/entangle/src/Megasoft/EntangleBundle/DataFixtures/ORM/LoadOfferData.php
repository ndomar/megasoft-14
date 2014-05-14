<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class LoadOfferData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $offer = new \Megasoft\EntangleBundle\Entity\Offer();
        $offer->setUser($this->getReference('sampleTangle'));
        $userTangle->setTangle($this->getReference('sampleTangle'));
        $userTangle->setUser($this->getReference('sampleUser'));
        $userTangle->setTangleOwner(true);
        
        $manager->persist($userTangle);
        $manager->flush();
       
        $this->addReference('sampleUserTangle', $userTangle);
    }
    
    
    
}