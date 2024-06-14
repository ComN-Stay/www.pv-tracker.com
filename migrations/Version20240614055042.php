<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240614055042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy ADD fk_system_id INT NOT NULL');
        $this->addSql('ALTER TABLE energy ADD CONSTRAINT FK_97117991B1CCF835 FOREIGN KEY (fk_system_id) REFERENCES systems (id)');
        $this->addSql('CREATE INDEX IDX_97117991B1CCF835 ON energy (fk_system_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE energy DROP FOREIGN KEY FK_97117991B1CCF835');
        $this->addSql('DROP INDEX IDX_97117991B1CCF835 ON energy');
        $this->addSql('ALTER TABLE energy DROP fk_system_id');
    }
}
