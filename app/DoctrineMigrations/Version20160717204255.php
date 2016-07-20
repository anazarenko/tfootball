<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160717204255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tournaments ADD winner_id INT DEFAULT NULL, ADD runner_up_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tournaments ADD CONSTRAINT FK_E4BCFAC35DFCD4B8 FOREIGN KEY (winner_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE tournaments ADD CONSTRAINT FK_E4BCFAC3BA774FBE FOREIGN KEY (runner_up_id) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_E4BCFAC35DFCD4B8 ON tournaments (winner_id)');
        $this->addSql('CREATE INDEX IDX_E4BCFAC3BA774FBE ON tournaments (runner_up_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tournaments DROP FOREIGN KEY FK_E4BCFAC35DFCD4B8');
        $this->addSql('ALTER TABLE tournaments DROP FOREIGN KEY FK_E4BCFAC3BA774FBE');
        $this->addSql('DROP INDEX IDX_E4BCFAC35DFCD4B8 ON tournaments');
        $this->addSql('DROP INDEX IDX_E4BCFAC3BA774FBE ON tournaments');
        $this->addSql('ALTER TABLE tournaments DROP winner_id, DROP runner_up_id');
    }
}
