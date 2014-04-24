<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140424190013 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
       
        $this->addSql("ALTER TABLE UserEmail ADD verified tinyint(1) DEFAULT 0");
        $this->addSql("ALTER TABLE User ADD accpetMailNotifications tinyint(1) DEFAULT 1");
        $this->addSql("ALTER TABLE InvitationCode ADD tangleId INT NOT NULL");
        $this->addSql("TRUNCATE TABLE  InvitationCode");
        $this->addSql("ALTER TABLE InvitationCode ADD CONSTRAINT FK_81E239712D9DD258 FOREIGN KEY (tangleId) REFERENCES Tangle (id)");
        $this->addSql("CREATE INDEX IDX_81E239712D9DD258 ON InvitationCode (tangleId)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE InvitationCode DROP FOREIGN KEY FK_81E239712D9DD258");
        $this->addSql("DROP INDEX IDX_81E239712D9DD258 ON InvitationCode");
        $this->addSql("ALTER TABLE InvitationCode DROP tangleId");
        $this->addSql("ALTER TABLE User DROP accpetMailNotifications");
        $this->addSql("ALTER TABLE UserEmail DROP verified");
    }
}
