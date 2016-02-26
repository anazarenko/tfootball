<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160226235446 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE statistics ADD created_at DATETIME NOT NULL, ADD modified_at DATETIME NOT NULL, CHANGE won won INT DEFAULT 0 NOT NULL, CHANGE drawn drawn INT DEFAULT 0 NOT NULL, CHANGE lost lost INT DEFAULT 0 NOT NULL, CHANGE game_count game_count INT DEFAULT 0 NOT NULL, CHANGE won_percentage won_percentage INT DEFAULT 0 NOT NULL, CHANGE biggest_victories biggest_victories LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE biggest_defeats biggest_defeats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE statistics DROP created_at, DROP modified_at, CHANGE won won INT NOT NULL, CHANGE drawn drawn INT NOT NULL, CHANGE lost lost INT NOT NULL, CHANGE game_count game_count INT NOT NULL, CHANGE won_percentage won_percentage DOUBLE PRECISION NOT NULL, CHANGE biggest_victories biggest_victories LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE biggest_defeats biggest_defeats LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
