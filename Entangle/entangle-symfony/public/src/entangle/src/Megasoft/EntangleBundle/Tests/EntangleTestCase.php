<?php

namespace Megasoft\EntangleBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntangleTestCase extends WebTestCase
{
    /* @var \Doctrine\ORM\EntityManager $em */
    public $em =  null;

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
        foreach ($tables as $table)
        {
            self::truncateTable($table->getName(),$connection);
        }
        $connection->exec('SET foreign_key_checks = 1');

        parent::setUp();
    }

    protected static function truncateTable($tableName,$connection)
    {
        $sql = sprintf('TRUNCATE TABLE %s', $tableName);
        $connection->exec($sql);
    }
}
