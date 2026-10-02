<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002063945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_customer_request (service_id INT NOT NULL, customer_request_id INT NOT NULL, INDEX IDX_800F0C30ED5CA9E6 (service_id), INDEX IDX_800F0C30BFB7BC27 (customer_request_id), PRIMARY KEY (service_id, customer_request_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE service_customer_request ADD CONSTRAINT FK_800F0C30ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_customer_request ADD CONSTRAINT FK_800F0C30BFB7BC27 FOREIGN KEY (customer_request_id) REFERENCES customer_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_request CHANGE date_req date_request DATE NOT NULL');
        $this->addSql('ALTER TABLE project ADD fk_service_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE1D326375 FOREIGN KEY (fk_service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE1D326375 ON project (fk_service_id)');
        $this->addSql('ALTER TABLE project_image ADD fk_project_id INT NOT NULL');
        $this->addSql('ALTER TABLE project_image ADD CONSTRAINT FK_D6680DC1E603D50F FOREIGN KEY (fk_project_id) REFERENCES project (id)');
        $this->addSql('CREATE INDEX IDX_D6680DC1E603D50F ON project_image (fk_project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_customer_request DROP FOREIGN KEY FK_800F0C30ED5CA9E6');
        $this->addSql('ALTER TABLE service_customer_request DROP FOREIGN KEY FK_800F0C30BFB7BC27');
        $this->addSql('DROP TABLE service_customer_request');
        $this->addSql('ALTER TABLE customer_request CHANGE date_request date_req DATE NOT NULL');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE1D326375');
        $this->addSql('DROP INDEX IDX_2FB3D0EE1D326375 ON project');
        $this->addSql('ALTER TABLE project DROP fk_service_id');
        $this->addSql('ALTER TABLE project_image DROP FOREIGN KEY FK_D6680DC1E603D50F');
        $this->addSql('DROP INDEX IDX_D6680DC1E603D50F ON project_image');
        $this->addSql('ALTER TABLE project_image DROP fk_project_id');
    }
}
