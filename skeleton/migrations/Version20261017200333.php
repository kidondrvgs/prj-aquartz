<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261017200333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les prix de démonstration, les réponses et le total des demandes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_request ADD reponse LONGTEXT DEFAULT NULL, ADD montant_estime INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD prix INT DEFAULT NULL');

        // Tarifs de démonstration en centimes pour les prestations existantes
        $prix = [
            'terrassement' => 120000,
            'assainissement' => 85000,
            'tranchees' => 45000,
            'demolition' => 65000,
            'amenagement-exterieur' => 90000,
            'location-pelle' => 35000,
            'preparation-piscine' => 150000,
            'evacuation-terres' => 28000,
            'pose-paves' => 60000,
            'apport-graviers' => 18000,
            'nivellement-terrain' => 40000,
            'preparation-acces' => 75000,
        ];

        foreach ($prix as $slug => $montant) {
            $this->addSql('UPDATE service SET prix = ? WHERE slug = ? AND prix IS NULL', [$montant, $slug]);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_request DROP reponse, DROP montant_estime');
        $this->addSql('ALTER TABLE service DROP prix');
    }
}
