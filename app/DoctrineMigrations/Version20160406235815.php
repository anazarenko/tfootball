<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160406235815 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tournaments (id INT AUTO_INCREMENT NOT NULL, user_creator_id INT DEFAULT NULL, form INT NOT NULL, status INT NOT NULL, regular_game_count INT NOT NULL, playoff_game_count INT NOT NULL, final_game_count INT NOT NULL, playoff_team_count INT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_E4BCFAC3C645C84A (user_creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_teams (tournament_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_5794B24133D1A3E7 (tournament_id), INDEX IDX_5794B241296CD8AE (team_id), PRIMARY KEY(tournament_id, team_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournament_statistics (id INT AUTO_INCREMENT NOT NULL, tournament_id INT DEFAULT NULL, team_id INT DEFAULT NULL, won INT DEFAULT 0 NOT NULL, drawn INT DEFAULT 0 NOT NULL, lost INT DEFAULT 0 NOT NULL, game_count INT DEFAULT 0 NOT NULL, won_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, points INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_88B32B7833D1A3E7 (tournament_id), INDEX IDX_88B32B78296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tournaments ADD CONSTRAINT FK_E4BCFAC3C645C84A FOREIGN KEY (user_creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tournament_teams ADD CONSTRAINT FK_5794B24133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_teams ADD CONSTRAINT FK_5794B241296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_statistics ADD CONSTRAINT FK_88B32B7833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id)');
        $this->addSql('ALTER TABLE tournament_statistics ADD CONSTRAINT FK_88B32B78296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD tournament INT DEFAULT NULL, ADD stage SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31BD5FB8D9 FOREIGN KEY (tournament) REFERENCES tournaments (id)');
        $this->addSql('CREATE INDEX IDX_FF232B31BD5FB8D9 ON games (tournament)');
        $this->addSql('UPDATE games SET form = 1 WHERE 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE games SET form = 0 WHERE 1');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31BD5FB8D9');
        $this->addSql('ALTER TABLE tournament_teams DROP FOREIGN KEY FK_5794B24133D1A3E7');
        $this->addSql('ALTER TABLE tournament_statistics DROP FOREIGN KEY FK_88B32B7833D1A3E7');
        $this->addSql('DROP TABLE tournaments');
        $this->addSql('DROP TABLE tournament_teams');
        $this->addSql('DROP TABLE tournament_statistics');
        $this->addSql('DROP INDEX IDX_FF232B31BD5FB8D9 ON games');
        $this->addSql('ALTER TABLE games DROP tournament, DROP stage');
    }
}
