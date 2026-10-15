<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261015211948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_request ADD CONSTRAINT FK_274A2B21A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE1D326375 FOREIGN KEY (fk_service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE project_image ADD CONSTRAINT FK_D6680DC1E603D50F FOREIGN KEY (fk_project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE service_customer_request ADD CONSTRAINT FK_800F0C30ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_customer_request ADD CONSTRAINT FK_800F0C30BFB7BC27 FOREIGN KEY (customer_request_id) REFERENCES customer_request (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_request DROP FOREIGN KEY FK_274A2B21A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE1D326375');
        $this->addSql('ALTER TABLE project_image DROP FOREIGN KEY FK_D6680DC1E603D50F');
        $this->addSql('ALTER TABLE service_customer_request DROP FOREIGN KEY FK_800F0C30ED5CA9E6');
        $this->addSql('ALTER TABLE service_customer_request DROP FOREIGN KEY FK_800F0C30BFB7BC27');
    }
}
