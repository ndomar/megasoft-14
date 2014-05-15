<?php
/**
 * Created by PhpStorm.
 * User: almgohar
 * Date: 5/15/14
 * Time: 4:24 PM
 */

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Megasoft\EntangleBundle\Entity\Message;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;

/**
 * Class LoadMessageData
 * @package Megasoft\EntangleBundle\DataFixtures\ORM
 * @author Almgohar
 */
class LoadMessageData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $message = new Message();
        $message->setOffer($this->getReference('sampleOffer'));
        $message->setDate(new DateTime('now'));
        $message->setSender($this->getReference('sampleUser1'));
        $message->setBody('This is a message.');
        $message->setDeleted(false);

        $manager->persist($message);

        $message1 = new Message();
        $message1->setOffer($this->getReference('sampleOffer'));
        $message1->setDate(new DateTime('now'));
        $message1->setSender($this->getReference('sampleUser'));
        $message1->setBody('This is a message too.');
        $message1->setDeleted(true);

        $manager->persist($message1);
        $manager->flush();

        $this->addReference('sampleMessage',$message);
        $this->addReference('SampleMessage1',$message1);

    }

    public function getOrder()
    {
        return 8;
    }
} 