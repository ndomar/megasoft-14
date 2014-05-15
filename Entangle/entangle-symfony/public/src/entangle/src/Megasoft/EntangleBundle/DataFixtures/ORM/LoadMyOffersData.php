<?php

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Session;
use Megasoft\EntangleBundle\Entity\User;
use Megasoft\EntangleBundle\Entity\UserTangle;
use Megasoft\EntangleBundle\Entity\Request;
use Megasoft\EntangleBundle\Entity\Tangle;
use Megasoft\EntangleBundle\Entity\Offer;

/*
 * Fixtures for view my offers end-point
 */
class LoadMyOffersData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->makeUsers($manager);
        $this->makeSessions($manager);
        $this->makeTangles($manager);
        $this->makeUserTangles($manager);
        $this->makeRequests($manager);
        $this->makeOffers($manager);                
        
        $manager->flush();
    }

    
}