<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240224121520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jour_reservation (id INT AUTO_INCREMENT NOT NULL, semaine_reservation_id INT NOT NULL, date_jour DATE NOT NULL, INDEX IDX_E43BD3A36CCF0206 (semaine_reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promo (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, numero INT NOT NULL, INDEX IDX_B0139AFBD823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repas (id INT AUTO_INCREMENT NOT NULL, type_repas_id INT NOT NULL, jour_reservation_id INT NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_A8D351B3D0DC4D56 (type_repas_id), INDEX IDX_A8D351B3A6615915 (jour_reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repas_reserve (id INT AUTO_INCREMENT NOT NULL, reservation_id INT NOT NULL, repas_id INT NOT NULL, INDEX IDX_628E82CEB83297E7 (reservation_id), INDEX IDX_628E82CE1D236AAA (repas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, semaine_id INT NOT NULL, somme_total DOUBLE PRECISION NOT NULL, INDEX IDX_42C84955FB88E14F (utilisateur_id), INDEX IDX_42C84955122EEC90 (semaine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semaine_reservation (id INT AUTO_INCREMENT NOT NULL, date_fin DATE NOT NULL, date_debut DATE NOT NULL, numero_semaine INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_repas (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, tarif_plein DOUBLE PRECISION NOT NULL, tarif_reduit DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_info_id INT DEFAULT NULL, identifiant VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649C90409EC (identifiant), UNIQUE INDEX UNIQ_8D93D649586DFF2 (user_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_info (id INT AUTO_INCREMENT NOT NULL, promo_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, age INT DEFAULT NULL, somme DOUBLE PRECISION NOT NULL, INDEX IDX_B1087D9ED0C07AFF (promo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jour_reservation ADD CONSTRAINT FK_E43BD3A36CCF0206 FOREIGN KEY (semaine_reservation_id) REFERENCES semaine_reservation (id)');
        $this->addSql('ALTER TABLE promo ADD CONSTRAINT FK_B0139AFBD823E37A FOREIGN KEY (section_id) REFERENCES section (id)');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B3D0DC4D56 FOREIGN KEY (type_repas_id) REFERENCES type_repas (id)');
        $this->addSql('ALTER TABLE repas ADD CONSTRAINT FK_A8D351B3A6615915 FOREIGN KEY (jour_reservation_id) REFERENCES jour_reservation (id)');
        $this->addSql('ALTER TABLE repas_reserve ADD CONSTRAINT FK_628E82CEB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE repas_reserve ADD CONSTRAINT FK_628E82CE1D236AAA FOREIGN KEY (repas_id) REFERENCES repas (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955122EEC90 FOREIGN KEY (semaine_id) REFERENCES semaine_reservation (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649586DFF2 FOREIGN KEY (user_info_id) REFERENCES user_info (id)');
        $this->addSql('ALTER TABLE user_info ADD CONSTRAINT FK_B1087D9ED0C07AFF FOREIGN KEY (promo_id) REFERENCES promo (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jour_reservation DROP FOREIGN KEY FK_E43BD3A36CCF0206');
        $this->addSql('ALTER TABLE promo DROP FOREIGN KEY FK_B0139AFBD823E37A');
        $this->addSql('ALTER TABLE repas DROP FOREIGN KEY FK_A8D351B3D0DC4D56');
        $this->addSql('ALTER TABLE repas DROP FOREIGN KEY FK_A8D351B3A6615915');
        $this->addSql('ALTER TABLE repas_reserve DROP FOREIGN KEY FK_628E82CEB83297E7');
        $this->addSql('ALTER TABLE repas_reserve DROP FOREIGN KEY FK_628E82CE1D236AAA');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FB88E14F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955122EEC90');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649586DFF2');
        $this->addSql('ALTER TABLE user_info DROP FOREIGN KEY FK_B1087D9ED0C07AFF');
        $this->addSql('DROP TABLE jour_reservation');
        $this->addSql('DROP TABLE promo');
        $this->addSql('DROP TABLE repas');
        $this->addSql('DROP TABLE repas_reserve');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE semaine_reservation');
        $this->addSql('DROP TABLE type_repas');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_info');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
