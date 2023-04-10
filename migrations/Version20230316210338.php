<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316210338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD id_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E1BD125E3 FOREIGN KEY (id_type_id) REFERENCES type_evenement (id)');
        $this->addSql('CREATE INDEX IDX_B26681E1BD125E3 ON evenement (id_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E1BD125E3');
        $this->addSql('DROP INDEX IDX_B26681E1BD125E3 ON evenement');
        $this->addSql('ALTER TABLE evenement DROP id_type_id');
    }
}
