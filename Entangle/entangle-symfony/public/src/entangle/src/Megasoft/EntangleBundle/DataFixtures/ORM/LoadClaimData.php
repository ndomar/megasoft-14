<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class LoadClaimData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $claim = new \Megasoft\EntangleBundle\Entity\Claim();
        $claim->setCreated(new \DateTime("now"));
        $claim->setClaimer('salma');
        $claim->setClaimerId(1);
        $claim->setTangle('7amada');
        $claim->setTangleId(1);
        $offer = new \Megasoft\EntangleBundle\Entity\Offer();
        $claim->setOffer($offer);
        $claim->setOfferId(1);
        $claim->setMessage('er7amony b2a');
        $claim->setDeleted(false);
        $manager->persist($claim);
        $manager->persist($offer);
        $manager->flush();
        
        $this->addReference('offer', $offer);
    }
    
    public function getOrder()
    {
        return 1;
    }
}
