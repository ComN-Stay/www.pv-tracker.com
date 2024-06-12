<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240601052813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE months ADD fk_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE months ADD CONSTRAINT FK_F2E3DC2E5741EEB9 FOREIGN KEY (fk_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_F2E3DC2E5741EEB9 ON months (fk_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE months DROP FOREIGN KEY FK_F2E3DC2E5741EEB9');
        $this->addSql('DROP INDEX IDX_F2E3DC2E5741EEB9 ON months');
        $this->addSql('ALTER TABLE months DROP fk_user_id');
    }
}
