<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115205108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP CONSTRAINT fk_794381c62f68b530');
        $this->addSql('DROP INDEX idx_794381c62f68b530');
        $this->addSql('ALTER TABLE review RENAME COLUMN group_id_id TO group_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6FE54D947 FOREIGN KEY (group_id) REFERENCES review_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_794381C6FE54D947 ON review (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6FE54D947');
        $this->addSql('DROP INDEX IDX_794381C6FE54D947');
        $this->addSql('ALTER TABLE review RENAME COLUMN group_id TO group_id_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT fk_794381c62f68b530 FOREIGN KEY (group_id_id) REFERENCES review_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_794381c62f68b530 ON review (group_id_id)');
    }
}
