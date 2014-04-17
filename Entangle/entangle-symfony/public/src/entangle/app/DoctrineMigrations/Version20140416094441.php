<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140416094441 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Claim ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE UserEmail ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Request ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Tangle ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Transaction ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Offer ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Message ADD deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE PendingInvitation ADD approved tinyint(1) DEFAULT 0");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Claim DROP deleted");
        $this->addSql("ALTER TABLE Message DROP deleted");
        $this->addSql("ALTER TABLE Offer DROP deleted");
        $this->addSql("ALTER TABLE PendingInvitation DROP approved");
        $this->addSql("ALTER TABLE Request DROP deleted");
        $this->addSql("ALTER TABLE Tangle DROP deleted");
        $this->addSql("ALTER TABLE Transaction DROP deleted");
        $this->addSql("ALTER TABLE UserEmail DROP deleted");
    }
}
