<?php

namespace Megasoft\EntangleBundle\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
 * Custom Class to extend PHPUnit's WebTestCase class and override setup
 * to truncate all tables of the database at the beginning of every test case
 */
class EntangleTestCase extends WebTestCase
{
    /* @var \Doctrine\ORM\EntityManager $em */
    public $em =  null;
    
    /* @var \Doctrine\Common\DataFixtures\Loader $loader */
    public $loader = null;
    
    /*
     * A method called at the beginning of every test
     * Overriden to truncate all tables of the database at the beginning of every test case
     */
    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $connection = $kernel->getContainer()
            ->get('doctrine')->getConnection();
        $connection->exec('SET foreign_key_checks = 0');
        /* @var \Doctrine\DBAL\Schema\MySqlSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();
        $tables = $schemaManager->listTables();
        foreach ($tables as $table){
            self::truncateTable($table->getName(),$connection);
        }
        $connection->exec('SET foreign_key_checks = 1');
        
        $this->loader = new Loader();
        parent::setUp();
    }
    
    /*
     * Helper function that takes a table name and a connectio to the database
     * and truncates that table
     * @param String $tableName table name
     * @param Connection $connection connection to database
     */
    protected static function truncateTable($tableName,$connection)
    {
        $sql = sprintf('TRUNCATE TABLE %s', $tableName);
        $connection->exec($sql);
    }
    
    public function addFixture($fixture){
        $this->loader->addFixture($fixture);
    }
    
    public function loadFixtures(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($this->loader->getFixtures());
    }
}
