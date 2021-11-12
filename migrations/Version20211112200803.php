<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112200803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE review (id INT NOT NULL, group_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, text TEXT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C62F68B530 ON review (group_id_id)');
        $this->addSql('CREATE TABLE review_review_tags (review_id INT NOT NULL, review_tags_id INT NOT NULL, PRIMARY KEY(review_id, review_tags_id))');
        $this->addSql('CREATE INDEX IDX_F3CE35423E2E969B ON review_review_tags (review_id)');
        $this->addSql('CREATE INDEX IDX_F3CE35421695FB43 ON review_review_tags (review_tags_id)');
        $this->addSql('CREATE TABLE review_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE review_tags (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C62F68B530 FOREIGN KEY (group_id_id) REFERENCES review_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_review_tags ADD CONSTRAINT FK_F3CE35423E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_review_tags ADD CONSTRAINT FK_F3CE35421695FB43 FOREIGN KEY (review_tags_id) REFERENCES review_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE review_review_tags DROP CONSTRAINT FK_F3CE35423E2E969B');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C62F68B530');
        $this->addSql('ALTER TABLE review_review_tags DROP CONSTRAINT FK_F3CE35421695FB43');
        $this->addSql('DROP SEQUENCE review_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_tags_id_seq CASCADE');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_review_tags');
        $this->addSql('DROP TABLE review_group');
        $this->addSql('DROP TABLE review_tags');
    }
}
