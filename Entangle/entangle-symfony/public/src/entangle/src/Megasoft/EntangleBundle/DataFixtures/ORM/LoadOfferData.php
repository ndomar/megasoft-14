<?php
/**
* Created by PhpStorm.
* User: almgohar
* Date: 5/14/14
* Time: 11:37 PM
*/

namespace Megasoft\EntangleBundle\DataFixtures\ORM;


use Megasoft\EntangleBundle\Entity\Offer;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;

/**
 * Class LoadOfferData
 * @package Megasoft\EntangleBundle\DataFixtures\ORM
 * @author Almgohar
 */
class LoadOfferData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $offer = new Offer();
        $offer->setStatus(1);
        $offer->setRequestedPrice(100);
        $offer->setDescription('This is an offer.');
        $offer->setUser($this->getReference('sampleUser1'));
        $offer->setRequest($this->getReference('sampleRequest'));
        $offer->setDeleted(false);
        $offer->setDate(new DateTime('now'));
        $offer->setExpectedDeadline(new DateTime('now'));

        $manager->persist($offer);

        $offer1 = new Offer();
        $offer1->setStatus(1);
        $offer1->setRequestedPrice(70);
        $offer1->setDescription('This is an offer.');
        $offer1->setUser($this->getReference('sampleUser1'));
        $offer1->setRequest($this->getReference('sampleRequest'));
        $offer1->setDeleted(false);
        $offer1->setDate(new DateTime('now'));
        $offer1->setExpectedDeadline(new DateTime('now'));

        $manager->persist($offer1);

        $offer2 = new Offer();
        $offer2->setStatus(1);
        $offer2->setRequestedPrice(70);
        $offer2->setDescription('This is an offer.');
        $offer2->setUser($this->getReference('sampleUser1'));
        $offer2->setRequest($this->getReference('sampleRequest1'));
        $offer2->setDeleted(true);
        $offer2->setDate(new DateTime('now'));
        $offer2->setExpectedDeadline(new DateTime('now'));

        $manager->persist($offer2);

        $offer3 = new Offer();
        $offer3->setStatus(1);
        $offer3->setRequestedPrice(70);
        $offer3->setDescription('This is an offer.');
        $offer3->setUser($this->getReference('sampleUser1'));
        $offer3->setRequest($this->getReference('sampleRequest2'));
        $offer3->setDeleted(false);
        $offer3->setDate(new DateTime('now'));
        $offer3->setExpectedDeadline(new DateTime('now'));

        $manager->persist($offer3);
        $manager->flush();

        $this->addReference('sampleOffer',$offer);
        $this->addReference('sampleOffer1',$offer1);
        $this->addReference('sampleOffer2',$offer2);
        $this->addReference('sampleOffer3',$offer3);



    }

    public function getOrder()
    {
        return 6;
    }
}

