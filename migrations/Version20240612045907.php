<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612045907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE systems (id INT AUTO_INCREMENT NOT NULL, fk_user_id INT NOT NULL, name VARCHAR(100) NOT NULL, system_id VARCHAR(255) DEFAULT NULL, token LONGTEXT DEFAULT NULL, refresh_token LONGTEXT DEFAULT NULL, INDEX IDX_61ADD8B25741EEB9 (fk_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE systems ADD CONSTRAINT FK_61ADD8B25741EEB9 FOREIGN KEY (fk_user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE systems DROP FOREIGN KEY FK_61ADD8B25741EEB9');
        $this->addSql('DROP TABLE systems');
    }
}
