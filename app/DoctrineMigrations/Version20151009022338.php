<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151009022338 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE drawn_games (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8BEB5D2BE48FD905 (game_id), INDEX IDX_8BEB5D2BA76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE drawn_games ADD CONSTRAINT FK_8BEB5D2BE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drawn_games ADD CONSTRAINT FK_8BEB5D2BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE games_drawn');
        $this->addSql('ALTER TABLE games DROP drawn');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE games_drawn (user_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_17D75E42A76ED395 (user_id), INDEX IDX_17D75E42E48FD905 (game_id), PRIMARY KEY(user_id, game_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games_drawn ADD CONSTRAINT FK_17D75E42A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games_drawn ADD CONSTRAINT FK_17D75E42E48FD905 FOREIGN KEY (game_id) REFERENCES games (id)');
        $this->addSql('DROP TABLE drawn_games');
        $this->addSql('ALTER TABLE games ADD drawn INT DEFAULT NULL');
    }
}
