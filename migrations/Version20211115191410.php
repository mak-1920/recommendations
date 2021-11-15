<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115191410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review RENAME COLUMN author_id TO authour_id_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6B41015A3 FOREIGN KEY (authour_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_794381C6B41015A3 ON review (authour_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6B41015A3');
        $this->addSql('DROP INDEX IDX_794381C6B41015A3');
        $this->addSql('ALTER TABLE review RENAME COLUMN authour_id_id TO author_id');
    }
}
