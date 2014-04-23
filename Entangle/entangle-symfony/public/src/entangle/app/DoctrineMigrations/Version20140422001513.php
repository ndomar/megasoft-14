<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140422001513 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32A1637001");
        $this->addSql("DROP INDEX IDX_A765AD32A1637001 ON Notification");
        $this->addSql("ALTER TABLE Notification ADD deletedOfferId INT DEFAULT NULL, ADD deletedRequestId INT DEFAULT NULL, ADD chosenOfferId INT DEFAULT NULL, CHANGE requestid priceChangeOfferId INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD323BED1114 FOREIGN KEY (priceChangeOfferId) REFERENCES Offer (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32AC4FC53B FOREIGN KEY (deletedOfferId) REFERENCES Offer (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32DEDEC483 FOREIGN KEY (deletedRequestId) REFERENCES Request (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32D5A141DB FOREIGN KEY (chosenOfferId) REFERENCES Offer (id)");
        $this->addSql("CREATE INDEX IDX_A765AD323BED1114 ON Notification (priceChangeOfferId)");
        $this->addSql("CREATE INDEX IDX_A765AD32AC4FC53B ON Notification (deletedOfferId)");
        $this->addSql("CREATE INDEX IDX_A765AD32DEDEC483 ON Notification (deletedRequestId)");
        $this->addSql("CREATE INDEX IDX_A765AD32D5A141DB ON Notification (chosenOfferId)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD323BED1114");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32AC4FC53B");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32DEDEC483");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32D5A141DB");
        $this->addSql("DROP INDEX IDX_A765AD323BED1114 ON Notification");
        $this->addSql("DROP INDEX IDX_A765AD32AC4FC53B ON Notification");
        $this->addSql("DROP INDEX IDX_A765AD32DEDEC483 ON Notification");
        $this->addSql("DROP INDEX IDX_A765AD32D5A141DB ON Notification");
        $this->addSql("ALTER TABLE Notification ADD requestId INT DEFAULT NULL, DROP priceChangeOfferId, DROP deletedOfferId, DROP deletedRequestId, DROP chosenOfferId");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32A1637001 FOREIGN KEY (requestId) REFERENCES Request (id)");
        $this->addSql("CREATE INDEX IDX_A765AD32A1637001 ON Notification (requestId)");
    }
}
