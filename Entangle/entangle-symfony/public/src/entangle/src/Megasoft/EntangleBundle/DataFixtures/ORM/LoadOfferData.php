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
        $manager->flush();

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
        $manager->flush();

        $this->addReference('sampleOffer',$offer);
        $this->addReference('sampleOffer1',$offer1);

    }

    public function getOrder()
    {
        return 6;
    }

} 