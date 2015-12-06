<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151205191510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE confirm (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, game_id INT DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_8FD3A344A76ED395 (user_id), INDEX IDX_8FD3A344E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, player_count INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_teams (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_71B58611296CD8AE (team_id), INDEX IDX_71B58611A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE confirm ADD CONSTRAINT FK_8FD3A344A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE confirm ADD CONSTRAINT FK_8FD3A344E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE users_teams ADD CONSTRAINT FK_71B58611296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_teams ADD CONSTRAINT FK_71B58611A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B315D1C595C');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3165EB6591');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31A40D7457');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31FB4741F6');
        $this->addSql('DROP INDEX IDX_FF232B315D1C595C ON games');
        $this->addSql('DROP INDEX IDX_FF232B31FB4741F6 ON games');
        $this->addSql('DROP INDEX IDX_FF232B3165EB6591 ON games');
        $this->addSql('DROP INDEX IDX_FF232B31A40D7457 ON games');
        $this->addSql('ALTER TABLE games ADD team_id INT DEFAULT NULL, ADD first_score INT DEFAULT NULL, ADD second_score INT DEFAULT NULL, DROP user_winner_id, DROP first_player_id, DROP second_player_id, DROP user_loser_id, DROP first_goals, DROP second_goals, DROP confirmed_first, DROP confirmed_second');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_FF232B31296CD8AE ON games (team_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31296CD8AE');
        $this->addSql('ALTER TABLE users_teams DROP FOREIGN KEY FK_71B58611296CD8AE');
        $this->addSql('DROP TABLE confirm');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE users_teams');
        $this->addSql('DROP INDEX IDX_FF232B31296CD8AE ON games');
        $this->addSql('ALTER TABLE games ADD user_winner_id INT DEFAULT NULL, ADD first_player_id INT DEFAULT NULL, ADD second_player_id INT DEFAULT NULL, ADD user_loser_id INT DEFAULT NULL, ADD first_goals INT DEFAULT NULL, ADD second_goals INT DEFAULT NULL, ADD confirmed_first INT NOT NULL, ADD confirmed_second INT DEFAULT NULL, DROP team_id, DROP first_score, DROP second_score');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B315D1C595C FOREIGN KEY (user_winner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3165EB6591 FOREIGN KEY (first_player_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31A40D7457 FOREIGN KEY (second_player_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31FB4741F6 FOREIGN KEY (user_loser_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_FF232B315D1C595C ON games (user_winner_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31FB4741F6 ON games (user_loser_id)');
        $this->addSql('CREATE INDEX IDX_FF232B3165EB6591 ON games (first_player_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31A40D7457 ON games (second_player_id)');
    }
}
