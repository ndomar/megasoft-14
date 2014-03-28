<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140328222517 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE InvitationCode (id INT AUTO_INCREMENT NOT NULL, inviterId INT NOT NULL, code VARCHAR(255) NOT NULL, created DATETIME NOT NULL, expired TINYINT(1) NOT NULL, userId INT DEFAULT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_81E2397164B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE VerificationCode (id INT AUTO_INCREMENT NOT NULL, verificationCode VARCHAR(255) NOT NULL, userId INT NOT NULL, created DATETIME NOT NULL, expired TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_6645F6764B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE InvitationCode ADD CONSTRAINT FK_81E2397164B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE VerificationCode ADD CONSTRAINT FK_6645F6764B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Offer ADD userId INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Offer ADD CONSTRAINT FK_E817A83A64B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("CREATE INDEX IDX_E817A83A64B64DCC ON Offer (userId)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE InvitationCode");
        $this->addSql("DROP TABLE VerificationCode");
        $this->addSql("ALTER TABLE Offer DROP FOREIGN KEY FK_E817A83A64B64DCC");
        $this->addSql("DROP INDEX IDX_E817A83A64B64DCC ON Offer");
        $this->addSql("ALTER TABLE Offer DROP userId");
    }
}
