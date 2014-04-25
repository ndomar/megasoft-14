<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140425151729 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Notification ADD newOfferId INT DEFAULT NULL, ADD newClaimId INT DEFAULT NULL, ADD reopenRequestId INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32D6B081EF FOREIGN KEY (newOfferId) REFERENCES Offer (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD325F074F18 FOREIGN KEY (newClaimId) REFERENCES Claim (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD325788EFB4 FOREIGN KEY (reopenRequestId) REFERENCES Request (id)");
        $this->addSql("CREATE INDEX IDX_A765AD32D6B081EF ON Notification (newOfferId)");
        $this->addSql("CREATE INDEX IDX_A765AD325F074F18 ON Notification (newClaimId)");
        $this->addSql("CREATE INDEX IDX_A765AD325788EFB4 ON Notification (reopenRequestId)");
        $this->addSql("ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F12364B64DCC");
        $this->addSql("DROP INDEX IDX_66A8F12364B64DCC ON Claim");
        $this->addSql("ALTER TABLE Claim ADD offerId INT NOT NULL, DROP userId, CHANGE usedid claimerId INT NOT NULL");
        $this->addSql("ALTER TABLE Claim ADD CONSTRAINT FK_66A8F12318A467DA FOREIGN KEY (offerId) REFERENCES Offer (id)");
        $this->addSql("ALTER TABLE Claim ADD CONSTRAINT FK_66A8F12357D25C06 FOREIGN KEY (claimerId) REFERENCES User (id)");
        $this->addSql("CREATE INDEX IDX_66A8F12318A467DA ON Claim (offerId)");
        $this->addSql("CREATE INDEX IDX_66A8F12357D25C06 ON Claim (claimerId)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F12318A467DA");
        $this->addSql("ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F12357D25C06");
        $this->addSql("DROP INDEX IDX_66A8F12318A467DA ON Claim");
        $this->addSql("DROP INDEX IDX_66A8F12357D25C06 ON Claim");
        $this->addSql("ALTER TABLE Claim ADD usedId INT NOT NULL, ADD userId INT DEFAULT NULL, DROP claimerId, DROP offerId");
        $this->addSql("ALTER TABLE Claim ADD CONSTRAINT FK_66A8F12364B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("CREATE INDEX IDX_66A8F12364B64DCC ON Claim (userId)");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32D6B081EF");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD325F074F18");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD325788EFB4");
        $this->addSql("DROP INDEX IDX_A765AD32D6B081EF ON Notification");
        $this->addSql("DROP INDEX IDX_A765AD325F074F18 ON Notification");
        $this->addSql("DROP INDEX IDX_A765AD325788EFB4 ON Notification");
        $this->addSql("ALTER TABLE Notification DROP newOfferId, DROP newClaimId, DROP reopenRequestId");
        
    }
}
