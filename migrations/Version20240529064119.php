<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529064119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE countries (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL, iso_code VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genders (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, short_name VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, team_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), INDEX IDX_7CE748A296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, fk_status_id INT NOT NULL, fk_gender_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, firstname VARCHAR(80) NOT NULL, lastname VARCHAR(80) NOT NULL, UNIQUE INDEX UNIQ_96C22258E7927C74 (email), INDEX IDX_96C22258AAED72D (fk_status_id), INDEX IDX_96C2225866517770 (fk_gender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, fk_gender_id INT NOT NULL, fk_status_id INT NOT NULL, fk_country_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, firstname VARCHAR(80) NOT NULL, lastname VARCHAR(80) NOT NULL, phone VARCHAR(25) DEFAULT NULL, mobile VARCHAR(25) DEFAULT NULL, address VARCHAR(255) NOT NULL, zip_code VARCHAR(20) NOT NULL, town VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E966517770 (fk_gender_id), INDEX IDX_1483A5E9AAED72D (fk_status_id), INDEX IDX_1483A5E9941F4E3 (fk_country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748A296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE teams ADD CONSTRAINT FK_96C22258AAED72D FOREIGN KEY (fk_status_id) REFERENCES user_statuses (id)');
        $this->addSql('ALTER TABLE teams ADD CONSTRAINT FK_96C2225866517770 FOREIGN KEY (fk_gender_id) REFERENCES genders (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E966517770 FOREIGN KEY (fk_gender_id) REFERENCES genders (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9AAED72D FOREIGN KEY (fk_status_id) REFERENCES user_statuses (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9941F4E3 FOREIGN KEY (fk_country_id) REFERENCES countries (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748A296CD8AE');
        $this->addSql('ALTER TABLE teams DROP FOREIGN KEY FK_96C22258AAED72D');
        $this->addSql('ALTER TABLE teams DROP FOREIGN KEY FK_96C2225866517770');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E966517770');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9AAED72D');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9941F4E3');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE genders');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE user_statuses');
        $this->addSql('DROP TABLE users');
    }
}
