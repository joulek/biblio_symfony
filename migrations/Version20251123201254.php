<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123201254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_item DROP FOREIGN KEY FK_747724FDE4D420FE');
        $this->addSql('DROP INDEX IDX_747724FDE4D420FE ON commande_item');
        $this->addSql('ALTER TABLE commande_item DROP commande_item_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_item ADD commande_item_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande_item ADD CONSTRAINT FK_747724FDE4D420FE FOREIGN KEY (commande_item_id) REFERENCES commande_item (id)');
        $this->addSql('CREATE INDEX IDX_747724FDE4D420FE ON commande_item (commande_item_id)');
    }
}
