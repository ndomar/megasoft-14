<?php

namespace Megasoft\EntangleBundle\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EntangleTestCase extends WebTestCase
{
    
    public function setup() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadTangleData());
        $loader->addFixture(new LoadUserData());
        $loader->addFixture(new LoadUserTangleData());

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
        parent::setup();
    }
    
    public function testSimpleGetAction(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/simpleget/sampleTangle', array(), array(), array('HTTP_X_SESSION_ID'=>'fdfdsffdsdf'), 'hello');

        $this->assertEquals('sampleUser', $client->getResponse()->getContent());
    }
}
