<?php

namespace Megasoft\EntangleBundle\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
 * Custom Class to extend PHPUnit's WebTestCase class and override setup
 * to truncate all tables of the database at the beginning of every test case
 * @author OmarElAzazy
 */
class EntangleTestCase extends WebTestCase
{
    /* @var \Doctrine\ORM\EntityManager $em */
    public $em =  null;

    /* @var \Doctrine\Common\DataFixtures\Loader $loader */
    public $loader = null;

    public $doctrine = null;
    
    /*
     * A method called at the beginning of every test
     * Overriden to truncate all tables of the database at the beginning of every test case
     * @author OmarElAzazy
     */
    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->doctrine = $kernel->getContainer()
            ->get('doctrine');
        $this->em = $this->doctrine->getManager();
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
     * @author OmarElAzazy
     */
    protected static function truncateTable($tableName,$connection)
    {
        $sql = sprintf('TRUNCATE TABLE %s', $tableName);
        $connection->exec($sql);
    }
    
    /*
     * Function to add a fixture to the list of fixtures to be loaded
     * @param \Doctrine\Common\DataFixtures\AbstractFixture $fixture fixture to be added
     * @author OmarElAzazy
     */
    public function addFixture($fixture){
        $this->loader->addFixture($fixture);
    }
    
    /*
     * Function to load all added fixtures
     * @author OmarElAzazy
     */
    public function loadFixtures(){
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($this->loader->getFixtures());
    }
}
