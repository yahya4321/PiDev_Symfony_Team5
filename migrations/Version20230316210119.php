<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316210119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_evenement ADD id INT AUTO_INCREMENT NOT NULL, DROP id_TypeEv, CHANGE domaine domaine VARCHAR(255) NOT NULL, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_evenement MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON type_evenement');
        $this->addSql('ALTER TABLE type_evenement ADD id_TypeEv INT NOT NULL, DROP id, CHANGE domaine domaine VARCHAR(40) NOT NULL');
    }
}
