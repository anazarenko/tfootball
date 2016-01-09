<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160108164351 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31296CD8AE');
        $this->addSql('DROP INDEX IDX_FF232B31296CD8AE ON games');
        $this->addSql('ALTER TABLE games ADD second_team INT DEFAULT NULL, CHANGE team_id first_team INT DEFAULT NULL');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B313312192B FOREIGN KEY (first_team) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3140A71EB1 FOREIGN KEY (second_team) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_FF232B313312192B ON games (first_team)');
        $this->addSql('CREATE INDEX IDX_FF232B3140A71EB1 ON games (second_team)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B313312192B');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3140A71EB1');
        $this->addSql('DROP INDEX IDX_FF232B313312192B ON games');
        $this->addSql('DROP INDEX IDX_FF232B3140A71EB1 ON games');
        $this->addSql('ALTER TABLE games ADD team_id INT DEFAULT NULL, DROP first_team, DROP second_team');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_FF232B31296CD8AE ON games (team_id)');
    }
}
