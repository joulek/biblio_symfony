<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204104300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id SERIAL NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE categorie (id SERIAL NOT NULL, designation VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE commande (id SERIAL NOT NULL, user_id INT NOT NULL, total DOUBLE PRECISION NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, adresse VARCHAR(255) NOT NULL, mode_paiement VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('COMMENT ON COLUMN commande.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE commande_item (id SERIAL NOT NULL, commande_id INT NOT NULL, livre_id INT NOT NULL, quantite INT NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_747724FD82EA2E54 ON commande_item (commande_id)');
        $this->addSql('CREATE INDEX IDX_747724FD37D925CB ON commande_item (livre_id)');
        $this->addSql('CREATE TABLE editeur (id SERIAL NOT NULL, nom VARCHAR(20) NOT NULL, pays VARCHAR(50) NOT NULL, adresse VARCHAR(100) NOT NULL, telephone INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE livre (id SERIAL NOT NULL, editeur_id INT NOT NULL, categorie_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, qte INT NOT NULL, prixunitaire DOUBLE PRECISION NOT NULL, datepub DATE NOT NULL, isbn INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC634F993375BD21 ON livre (editeur_id)');
        $this->addSql('CREATE INDEX IDX_AC634F99BCF5E72D ON livre (categorie_id)');
        $this->addSql('CREATE TABLE livre_auteur (livre_id INT NOT NULL, auteur_id INT NOT NULL, PRIMARY KEY(livre_id, auteur_id))');
        $this->addSql('CREATE INDEX IDX_A11876B537D925CB ON livre_auteur (livre_id)');
        $this->addSql('CREATE INDEX IDX_A11876B560BB6FE6 ON livre_auteur (auteur_id)');
        $this->addSql('CREATE TABLE panier (id SERIAL NOT NULL, user_id INT NOT NULL, total NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF2A76ED395 ON panier (user_id)');
        $this->addSql('CREATE TABLE panier_item (id SERIAL NOT NULL, panier_id INT NOT NULL, livre_id INT DEFAULT NULL, quantite INT NOT NULL, prix_total NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EBFD0067F77D927C ON panier_item (panier_id)');
        $this->addSql('CREATE INDEX IDX_EBFD006737D925CB ON panier_item (livre_id)');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, num_tel VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON users (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande_item ADD CONSTRAINT FK_747724FD82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commande_item ADD CONSTRAINT FK_747724FD37D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F993375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F99BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE livre_auteur ADD CONSTRAINT FK_A11876B537D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE livre_auteur ADD CONSTRAINT FK_A11876B560BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE panier_item ADD CONSTRAINT FK_EBFD0067F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE panier_item ADD CONSTRAINT FK_EBFD006737D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commande_item DROP CONSTRAINT FK_747724FD82EA2E54');
        $this->addSql('ALTER TABLE commande_item DROP CONSTRAINT FK_747724FD37D925CB');
        $this->addSql('ALTER TABLE livre DROP CONSTRAINT FK_AC634F993375BD21');
        $this->addSql('ALTER TABLE livre DROP CONSTRAINT FK_AC634F99BCF5E72D');
        $this->addSql('ALTER TABLE livre_auteur DROP CONSTRAINT FK_A11876B537D925CB');
        $this->addSql('ALTER TABLE livre_auteur DROP CONSTRAINT FK_A11876B560BB6FE6');
        $this->addSql('ALTER TABLE panier DROP CONSTRAINT FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE panier_item DROP CONSTRAINT FK_EBFD0067F77D927C');
        $this->addSql('ALTER TABLE panier_item DROP CONSTRAINT FK_EBFD006737D925CB');
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_item');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('DROP TABLE livre');
        $this->addSql('DROP TABLE livre_auteur');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_item');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
