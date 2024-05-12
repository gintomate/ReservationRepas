<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240510191333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jour_reservation ADD ferie TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE promo ADD nom_promo VARCHAR(255) NOT NULL, DROP numero');
        $this->addSql('ALTER TABLE reservation CHANGE somme_total montant_total DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE section ADD abreviation VARCHAR(255) NOT NULL, CHANGE intitule nom_section VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_info ADD date_de_naissance DATE NOT NULL, ADD montant_global DOUBLE PRECISION NOT NULL, DROP age');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_info ADD age INT DEFAULT NULL, DROP date_de_naissance, DROP montant_global');
        $this->addSql('ALTER TABLE jour_reservation DROP ferie');
        $this->addSql('ALTER TABLE reservation CHANGE montant_total somme_total DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE promo ADD numero INT NOT NULL, DROP nom_promo');
        $this->addSql('ALTER TABLE section ADD intitule VARCHAR(255) NOT NULL, DROP nom_section, DROP abreviation');
    }
}
