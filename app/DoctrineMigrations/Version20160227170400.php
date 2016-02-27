<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160227170400 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE confirms (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, game_id INT DEFAULT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_6A309921A76ED395 (user_id), INDEX IDX_6A309921E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, first_team INT DEFAULT NULL, second_team INT DEFAULT NULL, user_creator_id INT DEFAULT NULL, status SMALLINT NOT NULL, form SMALLINT NOT NULL, type SMALLINT NOT NULL, result INT DEFAULT NULL, first_score INT DEFAULT NULL, second_score INT DEFAULT NULL, game_date DATETIME NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_FF232B313312192B (first_team), INDEX IDX_FF232B3140A71EB1 (second_team), INDEX IDX_FF232B31C645C84A (user_creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statistics (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, won INT DEFAULT 0 NOT NULL, drawn INT DEFAULT 0 NOT NULL, lost INT DEFAULT 0 NOT NULL, game_count INT DEFAULT 0 NOT NULL, won_percentage INT DEFAULT 0 NOT NULL, biggest_victories LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', biggest_defeats LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', biggest_victory_difference SMALLINT DEFAULT \'0\' NOT NULL, biggest_defeats_difference SMALLINT DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E2D38B22296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, player_count INT NOT NULL, player_names LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_teams (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_71B58611296CD8AE (team_id), INDEX IDX_71B58611A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(60) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE players_games (user_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_E206029BA76ED395 (user_id), INDEX IDX_E206029BE48FD905 (game_id), PRIMARY KEY(user_id, game_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE confirms ADD CONSTRAINT FK_6A309921A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE confirms ADD CONSTRAINT FK_6A309921E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B313312192B FOREIGN KEY (first_team) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3140A71EB1 FOREIGN KEY (second_team) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31C645C84A FOREIGN KEY (user_creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE statistics ADD CONSTRAINT FK_E2D38B22296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE users_teams ADD CONSTRAINT FK_71B58611296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_teams ADD CONSTRAINT FK_71B58611A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE players_games ADD CONSTRAINT FK_E206029BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE players_games ADD CONSTRAINT FK_E206029BE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO users (username, password, email, roles, created_at, modified_at, is_active) VALUES ("Admin", "$2y$10$vVpS8lpDJpxZ3UhrMPc7kudTFvs0M9HbfVEnKBIkF4qSlrqLW5XjW", "admin@example.com", "a:1:{i:0;s:10:\"ROLE_ADMIN\";}", "2000-01-01 00:00:00", "2000-00-00 00:00:00", 1)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE confirms DROP FOREIGN KEY FK_6A309921E48FD905');
        $this->addSql('ALTER TABLE players_games DROP FOREIGN KEY FK_E206029BE48FD905');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B313312192B');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3140A71EB1');
        $this->addSql('ALTER TABLE statistics DROP FOREIGN KEY FK_E2D38B22296CD8AE');
        $this->addSql('ALTER TABLE users_teams DROP FOREIGN KEY FK_71B58611296CD8AE');
        $this->addSql('ALTER TABLE confirms DROP FOREIGN KEY FK_6A309921A76ED395');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31C645C84A');
        $this->addSql('ALTER TABLE users_teams DROP FOREIGN KEY FK_71B58611A76ED395');
        $this->addSql('ALTER TABLE players_games DROP FOREIGN KEY FK_E206029BA76ED395');
        $this->addSql('DROP TABLE confirms');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE statistics');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE users_teams');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE players_games');
    }
}
