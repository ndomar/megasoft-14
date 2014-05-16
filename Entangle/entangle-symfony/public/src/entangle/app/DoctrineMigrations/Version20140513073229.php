<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140513073229 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE ForgetPasswordCode (id INT AUTO_INCREMENT NOT NULL, forgetPasswordCode VARCHAR(255) NOT NULL, userId INT NOT NULL, created DATETIME NOT NULL, expired TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_EB6607A164B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE ForgetPasswordCode ADD CONSTRAINT FK_EB6607A164B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Notification ADD doneOfferId INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32EDB97E7A FOREIGN KEY (doneOfferId) REFERENCES Offer (id)");
        $this->addSql("CREATE INDEX IDX_A765AD32EDB97E7A ON Notification (doneOfferId)");
        $this->addSql("ALTER TABLE Claim ADD created DATETIME NOT NULL");
        $this->addSql("TRUNCATE VerificationCode");
        $this->addSql("ALTER TABLE VerificationCode DROP FOREIGN KEY FK_6645F6764B64DCC");
        $this->addSql("DROP INDEX UNIQ_6645F6764B64DCC ON VerificationCode");
        $this->addSql("ALTER TABLE VerificationCode CHANGE userid userEmailId INT NOT NULL");
        $this->addSql("ALTER TABLE VerificationCode ADD CONSTRAINT FK_6645F676B81E0A7 FOREIGN KEY (userEmailId) REFERENCES UserEmail (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_6645F676B81E0A7 ON VerificationCode (userEmailId)");
        $this->addSql("ALTER TABLE User DROP verified");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("DROP TABLE ForgetPasswordCode");
        $this->addSql("ALTER TABLE Claim DROP created");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32EDB97E7A");
        $this->addSql("DROP INDEX IDX_A765AD32EDB97E7A ON Notification");
        $this->addSql("ALTER TABLE Notification DROP doneOfferId");
        $this->addSql("ALTER TABLE User ADD verified TINYINT(1) NOT NULL");
        $this->addSql("ALTER TABLE VerificationCode DROP FOREIGN KEY FK_6645F676B81E0A7");
        $this->addSql("DROP INDEX UNIQ_6645F676B81E0A7 ON VerificationCode");
        $this->addSql("ALTER TABLE VerificationCode CHANGE useremailid userId INT NOT NULL");
        $this->addSql("ALTER TABLE VerificationCode ADD CONSTRAINT FK_6645F6764B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_6645F6764B64DCC ON VerificationCode (userId)");
    }
}
