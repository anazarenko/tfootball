<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007022903 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, user_first_id INT DEFAULT NULL, user_second_id INT DEFAULT NULL, user_winner_id INT DEFAULT NULL, user_loser_id INT DEFAULT NULL, status SMALLINT NOT NULL, form SMALLINT NOT NULL, type SMALLINT NOT NULL, first_goals INT DEFAULT NULL, second_goals INT DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, INDEX IDX_FF232B318C0865F (user_first_id), INDEX IDX_FF232B31FF769628 (user_second_id), INDEX IDX_FF232B315D1C595C (user_winner_id), INDEX IDX_FF232B31FB4741F6 (user_loser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B318C0865F FOREIGN KEY (user_first_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31FF769628 FOREIGN KEY (user_second_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B315D1C595C FOREIGN KEY (user_winner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31FB4741F6 FOREIGN KEY (user_loser_id) REFERENCES users (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE games');
    }
}
