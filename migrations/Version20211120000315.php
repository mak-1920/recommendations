<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211120000315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review_like_user DROP CONSTRAINT fk_c31791361e754ec5');
        $this->addSql('DROP SEQUENCE review_like_id_seq CASCADE');
        $this->addSql('DROP TABLE review_like');
        $this->addSql('DROP TABLE review_like_user');
        $this->addSql('DROP TABLE review_rating_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE review_like_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE review_like (id INT NOT NULL, review_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_4ed70dab3e2e969b ON review_like (review_id)');
        $this->addSql('CREATE TABLE review_like_user (review_like_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(review_like_id, user_id))');
        $this->addSql('CREATE INDEX idx_c31791361e754ec5 ON review_like_user (review_like_id)');
        $this->addSql('CREATE INDEX idx_c3179136a76ed395 ON review_like_user (user_id)');
        $this->addSql('CREATE TABLE review_rating_user (review_rating_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(review_rating_id, user_id))');
        $this->addSql('CREATE INDEX idx_372da9b09dc374c7 ON review_rating_user (review_rating_id)');
        $this->addSql('CREATE INDEX idx_372da9b0a76ed395 ON review_rating_user (user_id)');
        $this->addSql('ALTER TABLE review_like ADD CONSTRAINT fk_4ed70dab3e2e969b FOREIGN KEY (review_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_like_user ADD CONSTRAINT fk_c31791361e754ec5 FOREIGN KEY (review_like_id) REFERENCES review_like (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_like_user ADD CONSTRAINT fk_c3179136a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_rating_user ADD CONSTRAINT fk_372da9b09dc374c7 FOREIGN KEY (review_rating_id) REFERENCES review_rating (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_rating_user ADD CONSTRAINT fk_372da9b0a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
