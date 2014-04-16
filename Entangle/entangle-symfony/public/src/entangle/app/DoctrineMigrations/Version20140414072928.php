<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140414072928 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE InvitationMessage (id INT AUTO_INCREMENT NOT NULL, body VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE PendingInvitation (id INT AUTO_INCREMENT NOT NULL, inviteeId INT NOT NULL, inviterId INT NOT NULL, tangleId INT NOT NULL, messageId INT NOT NULL, INDEX IDX_96FCE61E4F508656 (inviterId), INDEX IDX_96FCE61E563933A3 (inviteeId), INDEX IDX_96FCE61E2D9DD258 (tangleId), INDEX IDX_96FCE61EA4C3A0DA (messageId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE PendingInvitation ADD CONSTRAINT FK_96FCE61E4F508656 FOREIGN KEY (inviterId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE PendingInvitation ADD CONSTRAINT FK_96FCE61E563933A3 FOREIGN KEY (inviteeId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE PendingInvitation ADD CONSTRAINT FK_96FCE61E2D9DD258 FOREIGN KEY (tangleId) REFERENCES Tangle (id)");
        $this->addSql("ALTER TABLE PendingInvitation ADD CONSTRAINT FK_96FCE61EA4C3A0DA FOREIGN KEY (messageId) REFERENCES InvitationMessage (id)");
        $this->addSql("ALTER TABLE Session ADD deviceType VARCHAR(255) NOT NULL, ADD regId VARCHAR(255) NOT NULL");
        $this->addSql("ALTER TABLE Offer CHANGE userId userId INT NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE PendingInvitation DROP FOREIGN KEY FK_96FCE61EA4C3A0DA");
        $this->addSql("DROP TABLE InvitationMessage");
        $this->addSql("DROP TABLE PendingInvitation");
        $this->addSql("ALTER TABLE Offer CHANGE userId userId INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Session DROP deviceType, DROP regId");
    }
}
