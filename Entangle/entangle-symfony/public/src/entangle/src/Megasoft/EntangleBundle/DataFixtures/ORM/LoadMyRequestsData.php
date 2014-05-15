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

/*
 * Fixtures for view my requests end-point
 * @author HebaAamer
 */
class LoadMyRequestsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}