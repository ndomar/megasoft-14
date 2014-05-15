<?php
/**
 * Created by PhpStorm.
 * User: almgohar
 * Date: 5/14/14
 * Time: 11:43 PM
 */

namespace Megasoft\EntangleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Megasoft\EntangleBundle\Entity\Transaction;
use DateTime;

class LoadTransactionData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $transaction = new Transaction();
        $transaction->setOffer($this->getReference('sampleOffer'));
        $transaction->setDeleted(false);
        $transaction->setFinalPrice(100);
        $transaction->setDate(new DateTime('now'));

        $manager->persist($transaction);
        $manager->flush();

        $transaction1 = new Transaction();
        $transaction1->setOffer($this->getReference('sampleOffer1'));
        $transaction1->setDeleted(true);
        $transaction1->setFinalPrice(100);
        $transaction1->setDate(new DateTime('now'));

        $manager->persist($transaction1);
        $manager->flush();

        $this->addReference('sampleTransaction',$transaction);
        $this->addReference('sampleTransaction1',$transaction1);

    }

    public function getOrder()
    {
        return 7;
    }

} 