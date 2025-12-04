<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204105337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emprunt (id SERIAL NOT NULL, relation_id INT DEFAULT NULL, livre_id INT NOT NULL, date_emprunt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_retour_prevue TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_retour_reelle TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, statut VARCHAR(255) NOT NULL, yes VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_364071D73256915B ON emprunt (relation_id)');
        $this->addSql('CREATE INDEX IDX_364071D737D925CB ON emprunt (livre_id)');
        $this->addSql('ALTER TABLE emprunt ADD CONSTRAINT FK_364071D73256915B FOREIGN KEY (relation_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE emprunt ADD CONSTRAINT FK_364071D737D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE emprunt DROP CONSTRAINT FK_364071D73256915B');
        $this->addSql('ALTER TABLE emprunt DROP CONSTRAINT FK_364071D737D925CB');
        $this->addSql('DROP TABLE emprunt');
    }
}
