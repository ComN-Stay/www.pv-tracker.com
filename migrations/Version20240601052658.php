<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240601052658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy ADD fk_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE energy ADD CONSTRAINT FK_971179915741EEB9 FOREIGN KEY (fk_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_971179915741EEB9 ON energy (fk_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy DROP FOREIGN KEY FK_971179915741EEB9');
        $this->addSql('DROP INDEX IDX_971179915741EEB9 ON energy');
        $this->addSql('ALTER TABLE energy DROP fk_user_id');
    }
}
