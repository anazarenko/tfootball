<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151009133631 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B311673C6A8');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B316E353359');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3181F75867');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31D7FD1968');
        $this->addSql('DROP INDEX IDX_FF232B3181F75867 ON games');
        $this->addSql('DROP INDEX IDX_FF232B31D7FD1968 ON games');
        $this->addSql('DROP INDEX IDX_FF232B316E353359 ON games');
        $this->addSql('DROP INDEX IDX_FF232B311673C6A8 ON games');
        $this->addSql('ALTER TABLE games ADD first_player_id INT DEFAULT NULL, ADD second_player_id INT DEFAULT NULL, ADD result INT DEFAULT NULL, DROP user_y_second_id, DROP user_y_first_id, DROP user_x_first_id, DROP user_x_second_id');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3165EB6591 FOREIGN KEY (first_player_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31A40D7457 FOREIGN KEY (second_player_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_FF232B3165EB6591 ON games (first_player_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31A40D7457 ON games (second_player_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3165EB6591');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31A40D7457');
        $this->addSql('DROP INDEX IDX_FF232B3165EB6591 ON games');
        $this->addSql('DROP INDEX IDX_FF232B31A40D7457 ON games');
        $this->addSql('ALTER TABLE games ADD user_y_second_id INT DEFAULT NULL, ADD user_y_first_id INT DEFAULT NULL, ADD user_x_first_id INT DEFAULT NULL, ADD user_x_second_id INT DEFAULT NULL, DROP first_player_id, DROP second_player_id, DROP result');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B311673C6A8 FOREIGN KEY (user_y_second_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B316E353359 FOREIGN KEY (user_y_first_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3181F75867 FOREIGN KEY (user_x_first_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31D7FD1968 FOREIGN KEY (user_x_second_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_FF232B3181F75867 ON games (user_x_first_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31D7FD1968 ON games (user_x_second_id)');
        $this->addSql('CREATE INDEX IDX_FF232B316E353359 ON games (user_y_first_id)');
        $this->addSql('CREATE INDEX IDX_FF232B311673C6A8 ON games (user_y_second_id)');
    }
}
