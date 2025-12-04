<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204182013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Suppression de la colonne note de la table emprunt
        $this->addSql('ALTER TABLE emprunt DROP COLUMN IF EXISTS note');
    }

    public function down(Schema $schema): void
    {
        // Ajout de la colonne note si on doit annuler la migration
        $this->addSql('ALTER TABLE emprunt ADD note INT DEFAULT NULL');
    }
}
