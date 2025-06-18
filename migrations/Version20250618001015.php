<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618001015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE job ADD job_alert_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8F444FA09 FOREIGN KEY (job_alert_id) REFERENCES job_alert (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FBD8E0F8F444FA09 ON job (job_alert_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE job_alert ALTER is_read SET NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE job DROP CONSTRAINT FK_FBD8E0F8F444FA09
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FBD8E0F8F444FA09
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE job DROP job_alert_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE job_alert ALTER is_read DROP NOT NULL
        SQL);
    }
}
