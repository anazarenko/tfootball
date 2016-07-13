<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160713154539 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games ADD winner INT DEFAULT NULL, ADD loser INT DEFAULT NULL');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31CF6600E FOREIGN KEY (winner) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31993D3FF FOREIGN KEY (loser) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_FF232B31CF6600E ON games (winner)');
        $this->addSql('CREATE INDEX IDX_FF232B31993D3FF ON games (loser)');
        $this->addSql('UPDATE games SET winner = first_team, loser = second_team WHERE result = 1');
        $this->addSql('UPDATE games SET winner = second_team, loser = first_team WHERE result = 2');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31CF6600E');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31993D3FF');
        $this->addSql('DROP INDEX IDX_FF232B31CF6600E ON games');
        $this->addSql('DROP INDEX IDX_FF232B31993D3FF ON games');
        $this->addSql('ALTER TABLE games DROP winner, DROP loser');
    }
}
