<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529042110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE months (id INT AUTO_INCREMENT NOT NULL, production NUMERIC(10, 2) DEFAULT NULL, import NUMERIC(10, 2) DEFAULT NULL, export NUMERIC(10, 2) DEFAULT NULL, self NUMERIC(10, 2) DEFAULT NULL, import_cost NUMERIC(10, 2) DEFAULT NULL, export_income NUMERIC(10, 2) DEFAULT NULL, savings NUMERIC(10, 2) DEFAULT NULL, balance NUMERIC(10, 2) DEFAULT NULL, consumption NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prices CHANGE amount amount NUMERIC(10, 7) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE months');
        $this->addSql('ALTER TABLE prices CHANGE amount amount NUMERIC(7, 4) NOT NULL');
    }
}
