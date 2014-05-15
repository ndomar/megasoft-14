<?php
/**
 * Created by PhpStorm.
 * User: almgohar
 * Date: 5/14/14
 * Time: 11:37 PM
 */

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Megasoft\EntangleBundle\Entity\Request;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;


/**
 * Class LoadRequestData
 * @package Megasoft\EntangleBundle\DataFixtures\ORM
 * @author Almgohar
 */
class LoadRequestData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $request = new Request();
        $request->setStatus(1);
        $request->setRequestedPrice(100);
        $request->setDescription('This is a request.');
        $request->setUser($this->getReference('sampleUser'));
        $request->setTangle($this->getReference('sampleTangle'));
        $request->setDate(new DateTime('now'));
        $request->setDeleted(false);

        $manager->persist($request);
        $manager->flush();

        $this->addReference('sampleRequest',$request);
    }

    public function getOrder()
    {
        return 5;
    }
}
