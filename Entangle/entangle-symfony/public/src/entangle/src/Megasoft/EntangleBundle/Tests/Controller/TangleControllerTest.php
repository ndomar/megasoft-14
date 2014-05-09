<?php

namespace Megasoft\EntangleBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadTangleData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserData;
use Megasoft\EntangleBundle\DataFixtures\ORM\LoadUserTangleData;
use Megasoft\EntangleBundle\Tests\EntangleTestCase;

class TangleControllerTest extends EntangleTestCase
{
    public function setup() {  
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $loader = new Loader();
        $loader->addFixture(new LoadTangleData());
        $loader->addFixture(new LoadUserData());
        $loader->addFixture(new LoadUserTangleData());

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
        parent::setup();
    }
    
    public function testSimpleGetAction(){
        $client = static::createClient();
        $crawler = $client->request('GET', 
                '/tangle/1/user', 
                array(), 
                array(), 
                array('HTTP_X_SESSION_ID'=>'fdfdsffdsdf'));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}
