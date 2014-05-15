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

        $request1 = new Request();
        $request1->setStatus(1);
        $request1->setRequestedPrice(100);
        $request1->setDescription('This is a request.');
        $request1->setUser($this->getReference('sampleUser'));
        $request1->setTangle($this->getReference('sampleTangle'));
        $request1->setDate(new DateTime('now'));
        $request1->setDeleted(false);

        $manager->persist($request1);

        $request2 = new Request();
        $request2->setStatus(1);
        $request2->setRequestedPrice(100);
        $request2->setDescription('This is a request.');
        $request2->setUser($this->getReference('sampleUser'));
        $request2->setTangle($this->getReference('sampleTangle'));
        $request2->setDate(new DateTime('now'));
        $request2->setDeleted(true);

        $manager->persist($request2);
        $manager->flush();

        $this->addReference('sampleRequest',$request);
        $this->addReference('sampleRequest1',$request1);
        $this->addReference('sampleRequest2',$request2);


    }

    public function getOrder()
    {
        return 5;
    }
}
