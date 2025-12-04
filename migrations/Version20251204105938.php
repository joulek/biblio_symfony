<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204105938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emprunt DROP CONSTRAINT fk_364071d73256915b');
        $this->addSql('DROP INDEX idx_364071d73256915b');
        $this->addSql('ALTER TABLE emprunt ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE emprunt DROP relation_id');
        $this->addSql('ALTER TABLE emprunt DROP yes');
        $this->addSql('ALTER TABLE emprunt ALTER statut TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE emprunt ADD CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE emprunt DROP CONSTRAINT FK_364071D7A76ED395');
        $this->addSql('DROP INDEX IDX_364071D7A76ED395');
        $this->addSql('ALTER TABLE emprunt ADD relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE emprunt ADD yes VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE emprunt DROP user_id');
        $this->addSql('ALTER TABLE emprunt ALTER statut TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE emprunt ADD CONSTRAINT fk_364071d73256915b FOREIGN KEY (relation_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_364071d73256915b ON emprunt (relation_id)');
    }
}
