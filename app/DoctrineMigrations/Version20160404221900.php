<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160404221900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tournaments (id INT AUTO_INCREMENT NOT NULL, user_creator_id INT DEFAULT NULL, regular_game_count INT NOT NULL, playoff_game_count INT NOT NULL, final_game_count INT NOT NULL, playoff_team_count INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_E4BCFAC3C645C84A (user_creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tournaments ADD CONSTRAINT FK_E4BCFAC3C645C84A FOREIGN KEY (user_creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD tournament INT DEFAULT NULL, ADD stage SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31BD5FB8D9 FOREIGN KEY (tournament) REFERENCES tournaments (id)');
        $this->addSql('CREATE INDEX IDX_FF232B31BD5FB8D9 ON games (tournament)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31BD5FB8D9');
        $this->addSql('DROP TABLE tournaments');
        $this->addSql('DROP INDEX IDX_FF232B31BD5FB8D9 ON games');
        $this->addSql('ALTER TABLE games DROP tournament, DROP stage');
    }
}
