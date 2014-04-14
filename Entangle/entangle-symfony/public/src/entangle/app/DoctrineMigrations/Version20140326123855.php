<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140326123855 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE Claim (id INT AUTO_INCREMENT NOT NULL, message VARCHAR(255) NOT NULL, status INT NOT NULL, usedId INT NOT NULL, tangleId INT NOT NULL, userId INT DEFAULT NULL, INDEX IDX_66A8F12364B64DCC (userId), INDEX IDX_66A8F1232D9DD258 (tangleId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Message (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, senderId INT NOT NULL, offerId INT NOT NULL, INDEX IDX_790009E3F0D67FFD (senderId), INDEX IDX_790009E318A467DA (offerId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Notification (id INT AUTO_INCREMENT NOT NULL, seen TINYINT(1) NOT NULL, created DATETIME NOT NULL, userId INT NOT NULL, type VARCHAR(255) NOT NULL, transactionId INT DEFAULT NULL, oldPrice INT DEFAULT NULL, newPrice INT DEFAULT NULL, requestId INT DEFAULT NULL, messageId INT DEFAULT NULL, INDEX IDX_A765AD3264B64DCC (userId), INDEX IDX_A765AD32C2F43114 (transactionId), INDEX IDX_A765AD32A1637001 (requestId), INDEX IDX_A765AD32A4C3A0DA (messageId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Offer (id INT AUTO_INCREMENT NOT NULL, requestedPrice INT NOT NULL, date DATETIME NOT NULL, description VARCHAR(255) NOT NULL, expectedDeadline DATE NOT NULL, status INT NOT NULL, requestId INT NOT NULL, INDEX IDX_E817A83AA1637001 (requestId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Request (id INT AUTO_INCREMENT NOT NULL, status INT NOT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, deadline DATE NOT NULL, icon VARCHAR(255) NOT NULL, requestedPrice INT NOT NULL, tangleId INT NOT NULL, userId INT NOT NULL, INDEX IDX_F42AB6032D9DD258 (tangleId), INDEX IDX_F42AB60364B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE request_tag (request_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_CEA3F6CF427EB8A5 (request_id), INDEX IDX_CEA3F6CFBAD26311 (tag_id), PRIMARY KEY(request_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Tangle (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Transaction (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, offerId INT NOT NULL, UNIQUE INDEX UNIQ_F4AB8A0618A467DA (offerId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, primaryEmailId INT NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, userBio VARCHAR(255) NOT NULL, birthDate DATE NOT NULL, verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_2DA17977F624432A (primaryEmailId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE UserEmail (id INT AUTO_INCREMENT NOT NULL, userId INT NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_8306ED0164B64DCC (userId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE UserTangle (id INT AUTO_INCREMENT NOT NULL, userId INT NOT NULL, tangleId INT NOT NULL, credit INT NOT NULL, tangleOwner TINYINT(1) NOT NULL, INDEX IDX_C7D94D4264B64DCC (userId), INDEX IDX_C7D94D422D9DD258 (tangleId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Claim ADD CONSTRAINT FK_66A8F12364B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Claim ADD CONSTRAINT FK_66A8F1232D9DD258 FOREIGN KEY (tangleId) REFERENCES Tangle (id)");
        $this->addSql("ALTER TABLE Message ADD CONSTRAINT FK_790009E3F0D67FFD FOREIGN KEY (senderId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Message ADD CONSTRAINT FK_790009E318A467DA FOREIGN KEY (offerId) REFERENCES Offer (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD3264B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32C2F43114 FOREIGN KEY (transactionId) REFERENCES Transaction (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32A1637001 FOREIGN KEY (requestId) REFERENCES Request (id)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD32A4C3A0DA FOREIGN KEY (messageId) REFERENCES Message (id)");
        $this->addSql("ALTER TABLE Offer ADD CONSTRAINT FK_E817A83AA1637001 FOREIGN KEY (requestId) REFERENCES Request (id)");
        $this->addSql("ALTER TABLE Request ADD CONSTRAINT FK_F42AB6032D9DD258 FOREIGN KEY (tangleId) REFERENCES Tangle (id)");
        $this->addSql("ALTER TABLE Request ADD CONSTRAINT FK_F42AB60364B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE request_tag ADD CONSTRAINT FK_CEA3F6CF427EB8A5 FOREIGN KEY (request_id) REFERENCES Request (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE request_tag ADD CONSTRAINT FK_CEA3F6CFBAD26311 FOREIGN KEY (tag_id) REFERENCES Tag (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Transaction ADD CONSTRAINT FK_F4AB8A0618A467DA FOREIGN KEY (offerId) REFERENCES Offer (id)");
        $this->addSql("ALTER TABLE User ADD CONSTRAINT FK_2DA17977F624432A FOREIGN KEY (primaryEmailId) REFERENCES UserEmail (id)");
        $this->addSql("ALTER TABLE UserEmail ADD CONSTRAINT FK_8306ED0164B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE UserTangle ADD CONSTRAINT FK_C7D94D4264B64DCC FOREIGN KEY (userId) REFERENCES User (id)");
        $this->addSql("ALTER TABLE UserTangle ADD CONSTRAINT FK_C7D94D422D9DD258 FOREIGN KEY (tangleId) REFERENCES Tangle (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32A4C3A0DA");
        $this->addSql("ALTER TABLE Message DROP FOREIGN KEY FK_790009E318A467DA");
        $this->addSql("ALTER TABLE Transaction DROP FOREIGN KEY FK_F4AB8A0618A467DA");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32A1637001");
        $this->addSql("ALTER TABLE Offer DROP FOREIGN KEY FK_E817A83AA1637001");
        $this->addSql("ALTER TABLE request_tag DROP FOREIGN KEY FK_CEA3F6CF427EB8A5");
        $this->addSql("ALTER TABLE request_tag DROP FOREIGN KEY FK_CEA3F6CFBAD26311");
        $this->addSql("ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F1232D9DD258");
        $this->addSql("ALTER TABLE Request DROP FOREIGN KEY FK_F42AB6032D9DD258");
        $this->addSql("ALTER TABLE UserTangle DROP FOREIGN KEY FK_C7D94D422D9DD258");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD32C2F43114");
        $this->addSql("ALTER TABLE Claim DROP FOREIGN KEY FK_66A8F12364B64DCC");
        $this->addSql("ALTER TABLE Message DROP FOREIGN KEY FK_790009E3F0D67FFD");
        $this->addSql("ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD3264B64DCC");
        $this->addSql("ALTER TABLE Request DROP FOREIGN KEY FK_F42AB60364B64DCC");
        $this->addSql("ALTER TABLE UserEmail DROP FOREIGN KEY FK_8306ED0164B64DCC");
        $this->addSql("ALTER TABLE UserTangle DROP FOREIGN KEY FK_C7D94D4264B64DCC");
        $this->addSql("ALTER TABLE User DROP FOREIGN KEY FK_2DA17977F624432A");
        $this->addSql("DROP TABLE Claim");
        $this->addSql("DROP TABLE Message");
        $this->addSql("DROP TABLE Notification");
        $this->addSql("DROP TABLE Offer");
        $this->addSql("DROP TABLE Request");
        $this->addSql("DROP TABLE request_tag");
        $this->addSql("DROP TABLE Tag");
        $this->addSql("DROP TABLE Tangle");
        $this->addSql("DROP TABLE Transaction");
        $this->addSql("DROP TABLE User");
        $this->addSql("DROP TABLE UserEmail");
        $this->addSql("DROP TABLE UserTangle");
    }
}
