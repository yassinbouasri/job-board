<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502154717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE profile (id SERIAL NOT NULL, user_profile_id INT NOT NULL, full_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8157AA0F6B9DD454 ON profile (user_profile_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F6B9DD454 FOREIGN KEY (user_profile_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE profile DROP CONSTRAINT FK_8157AA0F6B9DD454
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE profile
        SQL);
    }
}
