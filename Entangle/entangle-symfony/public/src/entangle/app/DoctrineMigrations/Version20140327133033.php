<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140327133033 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Offer CHANGE expectedDeadline expectedDeadline DATE DEFAULT NULL");
        $this->addSql("ALTER TABLE Request CHANGE deadline deadline DATE DEFAULT NULL, CHANGE icon icon VARCHAR(255) DEFAULT NULL, CHANGE requestedPrice requestedPrice INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Tangle CHANGE icon icon VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE User CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE userBio userBio VARCHAR(255) DEFAULT NULL, CHANGE birthDate birthDate DATE DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Offer CHANGE expectedDeadline expectedDeadline DATE NOT NULL");
        $this->addSql("ALTER TABLE Request CHANGE deadline deadline DATE NOT NULL, CHANGE icon icon VARCHAR(255) NOT NULL, CHANGE requestedPrice requestedPrice INT NOT NULL");
        $this->addSql("ALTER TABLE Tangle CHANGE icon icon VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE User CHANGE photo photo VARCHAR(255) NOT NULL, CHANGE userBio userBio VARCHAR(255) NOT NULL, CHANGE birthDate birthDate DATE NOT NULL");
    }
}
