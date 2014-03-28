<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140326131658 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE User DROP FOREIGN KEY FK_2DA17977F624432A");
        $this->addSql("DROP INDEX UNIQ_2DA17977F624432A ON User");
        $this->addSql("ALTER TABLE User DROP primaryEmailId");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE User ADD primaryEmailId INT NOT NULL");
        $this->addSql("ALTER TABLE User ADD CONSTRAINT FK_2DA17977F624432A FOREIGN KEY (primaryEmailId) REFERENCES UserEmail (id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_2DA17977F624432A ON User (primaryEmailId)");
    }
}
