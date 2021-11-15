<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115165718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE review_illustration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE review_illustration (id INT NOT NULL, review_id INT DEFAULT NULL, img VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FB661B93E2E969B ON review_illustration (review_id)');
        $this->addSql('ALTER TABLE review_illustration ADD CONSTRAINT FK_8FB661B93E2E969B FOREIGN KEY (review_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD date_of_publication TIMESTAMP(0) WITH TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN review.date_of_publication IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE review_illustration_id_seq CASCADE');
        $this->addSql('DROP TABLE review_illustration');
        $this->addSql('ALTER TABLE review DROP date_of_publication');
    }
}
