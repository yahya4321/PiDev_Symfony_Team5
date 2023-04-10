<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324104946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation_inscri DROP FOREIGN KEY formation_inscri_ibfk_1');
        $this->addSql('ALTER TABLE formation_inscri DROP FOREIGN KEY formation_inscri_ibfk_2');
        $this->addSql('ALTER TABLE participation_evenement DROP FOREIGN KEY participation_evenement_ibfk_1');
        $this->addSql('ALTER TABLE participation_evenement DROP FOREIGN KEY participation_evenement_ibfk_2');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_inscri');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE participation_evenement');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE service');
        $this->addSql('ALTER TABLE evenement MODIFY id_ev INT NOT NULL');
        $this->addSql('DROP INDEX id_type ON evenement');
        $this->addSql('DROP INDEX `primary` ON evenement');
        $this->addSql('ALTER TABLE evenement CHANGE image image VARCHAR(255) NOT NULL, CHANGE region region VARCHAR(255) NOT NULL, CHANGE id_ev id INT AUTO_INCREMENT NOT NULL, CHANGE id_type id_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E1BD125E3 FOREIGN KEY (id_type_id) REFERENCES type_evenement (id)');
        $this->addSql('CREATE INDEX IDX_B26681E1BD125E3 ON evenement (id_type_id)');
        $this->addSql('ALTER TABLE evenement ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE type_evenement MODIFY id_TypeEv INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON type_evenement');
        $this->addSql('ALTER TABLE type_evenement CHANGE domaine domaine VARCHAR(255) NOT NULL, CHANGE id_TypeEv id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE type_evenement ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', DROP role, CHANGE email email VARCHAR(180) NOT NULL, CHANGE numTel num_tel INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (CatId INT AUTO_INCREMENT NOT NULL, CatLib VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(CatId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE client (Date_inscri DATE NOT NULL, Etat_cl VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Statut VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Domaine VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE formation (Matricule INT AUTO_INCREMENT NOT NULL, Titre_fr VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Contenu VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, Niveau VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(Matricule)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE formation_inscri (Id_finscri INT AUTO_INCREMENT NOT NULL, Matricule INT NOT NULL, Id_inscri INT NOT NULL, INDEX Id_inscri (Id_inscri), INDEX Matricule (Matricule), PRIMARY KEY(Id_finscri)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE inscription (Id_inscri INT AUTO_INCREMENT NOT NULL, Disponibilite VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(Id_inscri)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE participation_evenement (id INT AUTO_INCREMENT NOT NULL, id_event INT NOT NULL, id_user INT NOT NULL, INDEX id_event (id_event), INDEX id_user (id_user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reclamation (Idrec INT AUTO_INCREMENT NOT NULL, nomuser VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, objet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, etatrec VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'En attente \' COLLATE `utf8mb4_general_ci`, PRIMARY KEY(Idrec)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE service (ServId INT AUTO_INCREMENT NOT NULL, ServLib VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ServDesc VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ServDispo INT NOT NULL, ServImg VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ServPrix DOUBLE PRECISION NOT NULL, ServVille VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CatLib VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(ServId)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE formation_inscri ADD CONSTRAINT formation_inscri_ibfk_1 FOREIGN KEY (Matricule) REFERENCES formation (Matricule)');
        $this->addSql('ALTER TABLE formation_inscri ADD CONSTRAINT formation_inscri_ibfk_2 FOREIGN KEY (Id_inscri) REFERENCES inscription (Id_inscri)');
        $this->addSql('ALTER TABLE participation_evenement ADD CONSTRAINT participation_evenement_ibfk_1 FOREIGN KEY (id_event) REFERENCES evenement (id_ev)');
        $this->addSql('ALTER TABLE participation_evenement ADD CONSTRAINT participation_evenement_ibfk_2 FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE evenement MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E1BD125E3');
        $this->addSql('DROP INDEX IDX_B26681E1BD125E3 ON evenement');
        $this->addSql('DROP INDEX `PRIMARY` ON evenement');
        $this->addSql('ALTER TABLE evenement CHANGE image image VARCHAR(500) NOT NULL, CHANGE region region VARCHAR(50) NOT NULL, CHANGE id id_ev INT AUTO_INCREMENT NOT NULL, CHANGE id_type_id id_type INT DEFAULT NULL');
        $this->addSql('CREATE INDEX id_type ON evenement (id_type)');
        $this->addSql('ALTER TABLE evenement ADD PRIMARY KEY (id_ev)');
        $this->addSql('ALTER TABLE type_evenement MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON type_evenement');
        $this->addSql('ALTER TABLE type_evenement CHANGE domaine domaine VARCHAR(40) NOT NULL, CHANGE id id_TypeEv INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE type_evenement ADD PRIMARY KEY (id_TypeEv)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON `user`');
        $this->addSql('ALTER TABLE `user` ADD role VARCHAR(255) NOT NULL, DROP roles, CHANGE email email VARCHAR(255) NOT NULL, CHANGE num_tel numTel INT NOT NULL');
    }
}
