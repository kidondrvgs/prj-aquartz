<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261016133755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer_request (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(15) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, location VARCHAR(255) NOT NULL, status VARCHAR(10) NOT NULL, surface INT NOT NULL, description LONGTEXT NOT NULL, date_request DATE NOT NULL, user_id INT NOT NULL, INDEX IDX_274A2B21A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, client_type VARCHAR(10) NOT NULL, location VARCHAR(255) NOT NULL, surface INT NOT NULL, duration VARCHAR(100) NOT NULL, date_completion DATE NOT NULL, fk_service_id INT NOT NULL, INDEX IDX_2FB3D0EE1D326375 (fk_service_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_image (id INT AUTO_INCREMENT NOT NULL, image_path VARCHAR(255) NOT NULL, alt_text VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, is_main TINYINT NOT NULL, fk_project_id INT NOT NULL, INDEX IDX_D6680DC1E603D50F (fk_project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, images JSON DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_customer_request (service_id INT NOT NULL, customer_request_id INT NOT NULL, INDEX IDX_800F0C30ED5CA9E6 (service_id), INDEX IDX_800F0C30BFB7BC27 (customer_request_id), PRIMARY KEY (service_id, customer_request_id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 ENGINE = InnoDB');
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
        $this->addSql('DROP TABLE customer_request');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_image');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_customer_request');
        $this->addSql('DROP TABLE user');
    }
}
