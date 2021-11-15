<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115191908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP CONSTRAINT fk_794381c6b41015a3');
        $this->addSql('DROP INDEX idx_794381c6b41015a3');
        $this->addSql('ALTER TABLE review RENAME COLUMN authour_id_id TO authour_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6677EBA4F FOREIGN KEY (authour_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_794381C6677EBA4F ON review (authour_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6677EBA4F');
        $this->addSql('DROP INDEX IDX_794381C6677EBA4F');
        $this->addSql('ALTER TABLE review RENAME COLUMN authour_id TO authour_id_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT fk_794381c6b41015a3 FOREIGN KEY (authour_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_794381c6b41015a3 ON review (authour_id_id)');
    }
}
