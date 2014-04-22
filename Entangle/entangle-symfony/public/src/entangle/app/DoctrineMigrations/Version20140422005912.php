<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140422005912 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE UnfreezeRequest (id INT AUTO_INCREMENT NOT NULL, userId INT NOT NULL, requestId INT NOT NULL, INDEX IDX_C961E2B8A1637001 (requestId), INDEX IDX_C961E2B864B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE UnfreezeRequest ADD CONSTRAINT FK_C961E2B8A1637001 FOREIGN KEY (requestId) REFERENCES Request (id)");
        $this->addSql("ALTER TABLE UnfreezeRequest ADD CONSTRAINT FK_C961E2B864B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE UserTangle ADD leavingDate DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE UnfreezeRequest");
        $this->addSql("ALTER TABLE UserTangle DROP leavingDate");
    }
}
