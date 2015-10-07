<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151008013839 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B318C0865F');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31FF769628');
        $this->addSql('DROP INDEX IDX_FF232B318C0865F ON games');
        $this->addSql('DROP INDEX IDX_FF232B31FF769628 ON games');
        $this->addSql('ALTER TABLE games ADD user_x_first_id INT DEFAULT NULL, ADD user_x_second_id INT DEFAULT NULL, ADD user_y_first_id INT DEFAULT NULL, ADD user_y_second_id INT DEFAULT NULL, DROP user_first_id, DROP user_second_id');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B3181F75867 FOREIGN KEY (user_x_first_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31D7FD1968 FOREIGN KEY (user_x_second_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B316E353359 FOREIGN KEY (user_y_first_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B311673C6A8 FOREIGN KEY (user_y_second_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_FF232B3181F75867 ON games (user_x_first_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31D7FD1968 ON games (user_x_second_id)');
        $this->addSql('CREATE INDEX IDX_FF232B316E353359 ON games (user_y_first_id)');
        $this->addSql('CREATE INDEX IDX_FF232B311673C6A8 ON games (user_y_second_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B3181F75867');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31D7FD1968');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B316E353359');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B311673C6A8');
        $this->addSql('DROP INDEX IDX_FF232B3181F75867 ON games');
        $this->addSql('DROP INDEX IDX_FF232B31D7FD1968 ON games');
        $this->addSql('DROP INDEX IDX_FF232B316E353359 ON games');
        $this->addSql('DROP INDEX IDX_FF232B311673C6A8 ON games');
        $this->addSql('ALTER TABLE games ADD user_first_id INT DEFAULT NULL, ADD user_second_id INT DEFAULT NULL, DROP user_x_first_id, DROP user_x_second_id, DROP user_y_first_id, DROP user_y_second_id');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B318C0865F FOREIGN KEY (user_first_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31FF769628 FOREIGN KEY (user_second_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_FF232B318C0865F ON games (user_first_id)');
        $this->addSql('CREATE INDEX IDX_FF232B31FF769628 ON games (user_second_id)');
    }
}
