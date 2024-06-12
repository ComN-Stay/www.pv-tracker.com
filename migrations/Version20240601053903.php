<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240601053903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indexes ADD fk_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE indexes ADD CONSTRAINT FK_5A92E8525741EEB9 FOREIGN KEY (fk_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_5A92E8525741EEB9 ON indexes (fk_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE indexes DROP FOREIGN KEY FK_5A92E8525741EEB9');
        $this->addSql('DROP INDEX IDX_5A92E8525741EEB9 ON indexes');
        $this->addSql('ALTER TABLE indexes DROP fk_user_id');
    }
}
