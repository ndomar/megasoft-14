<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140419191808 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Claim CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE UserEmail CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Request CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Tangle ADD deletedBalance INT NOT NULL, CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Transaction CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Offer CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE Message CHANGE deleted deleted tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE PendingInvitation CHANGE approved approved tinyint(1) DEFAULT 0");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Claim CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE Message CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE Offer CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE PendingInvitation CHANGE approved approved TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE Request CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE Tangle DROP deletedBalance, CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE Transaction CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
        $this->addSql("ALTER TABLE UserEmail CHANGE deleted deleted TINYINT(1) DEFAULT '0'");
    }
}