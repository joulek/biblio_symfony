<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204174403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id SERIAL NOT NULL, user_id INT NOT NULL, livre_id INT NOT NULL, valeur INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CFBDFA14A76ED395 ON note (user_id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA1437D925CB ON note (livre_id)');
        $this->addSql('CREATE UNIQUE INDEX user_livre_unique ON note (user_id, livre_id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1437D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE note DROP CONSTRAINT FK_CFBDFA14A76ED395');
        $this->addSql('ALTER TABLE note DROP CONSTRAINT FK_CFBDFA1437D925CB');
        $this->addSql('DROP TABLE note');
    }
}
