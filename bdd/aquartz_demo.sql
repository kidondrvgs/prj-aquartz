-- AQUARTZ : base de démonstration avec données fictives
-- Importer uniquement dans une base vide. Les comptes sont réservés aux tests.
SET NAMES utf8mb4;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `status` varchar(10) NOT NULL,
  `surface` int NOT NULL,
  `description` longtext NOT NULL,
  `date_request` date NOT NULL,
  `user_id` int NOT NULL,
  `reponse` longtext,
  `montant_estime` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_274A2B21A76ED395` (`user_id`),
  CONSTRAINT `FK_274A2B21A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `client_type` varchar(10) NOT NULL,
  `location` varchar(255) NOT NULL,
  `surface` int NOT NULL,
  `duration` varchar(100) NOT NULL,
  `date_completion` date NOT NULL,
  `fk_service_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2FB3D0EE1D326375` (`fk_service_id`),
  CONSTRAINT `FK_2FB3D0EE1D326375` FOREIGN KEY (`fk_service_id`) REFERENCES `service` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `position` int DEFAULT NULL,
  `is_main` tinyint NOT NULL,
  `fk_project_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D6680DC1E603D50F` (`fk_project_id`),
  CONSTRAINT `FK_D6680DC1E603D50F` FOREIGN KEY (`fk_project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `images` json DEFAULT NULL,
  `prix` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_customer_request` (
  `service_id` int NOT NULL,
  `customer_request_id` int NOT NULL,
  PRIMARY KEY (`service_id`,`customer_request_id`),
  KEY `IDX_800F0C30ED5CA9E6` (`service_id`),
  KEY `IDX_800F0C30BFB7BC27` (`customer_request_id`),
  CONSTRAINT `FK_800F0C30BFB7BC27` FOREIGN KEY (`customer_request_id`) REFERENCES `customer_request` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_800F0C30ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


START TRANSACTION;
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('1', 'Terrassement général', 'terrassement', 'Décaissement, nivellement, création de plateformes et évacuation des terres.', NULL, '120000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('2', 'Assainissement', 'assainissement', 'Fosses septiques, drainage, puisards et raccordements aux réseaux.', NULL, '85000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('3', 'Tranchées et réseaux', 'tranchees', 'Ouverture et remblaiement de tranchées pour eau, électricité et fibre.', NULL, '45000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('4', 'Démolition', 'demolition', 'Dépose de dalles, murets, terrasses et petites structures.', NULL, '65000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('5', 'Aménagement extérieur', 'amenagement-exterieur', 'Accès, cours, allées, voiries légères et finitions de terrain.', NULL, '90000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('6', 'Location de pelle avec chauffeur', 'location-pelle', 'Mise à disposition de mini-pelle ou pelle 8 tonnes à la demi-journée.', NULL, '35000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('7', 'Préparation de piscine', 'preparation-piscine', 'Creusement du bassin et préparation du terrain avant la pose de la piscine.', NULL, '150000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('8', 'Évacuation des terres', 'evacuation-terres', 'Chargement et transport des terres et déblais issus de votre chantier.', NULL, '28000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('9', 'Pose de pavés', 'pose-paves', 'Préparation du support et pose de pavés pour vos allées et terrasses.', NULL, '60000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('10', 'Apport de graviers', 'apport-graviers', 'Apport et répartition de graviers pour stabiliser les surfaces et aménager les abords.', NULL, '18000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('11', 'Nivellement de terrain', 'nivellement-terrain', 'Mise à niveau du terrain et répartition des terres avant vos aménagements.', NULL, '40000');
INSERT INTO `service` (`id`, `title`, `slug`, `description`, `images`, `prix`) VALUES ('12', 'Préparation des accès', 'preparation-acces', 'Préparation et stabilisation des accès pour les véhicules et les engins de chantier.', NULL, '75000');
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`) VALUES ('1', 'theo.client@example.test', '[]', '$2y$12$GBOMb/V87ZXnhaMUIiewkONkJ81dP9gVrSn29lh8utVViaWyc3KbS', 'Théo', 'Démo');
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`) VALUES ('2', 'theo.admin@example.test', '[\"ROLE_ADMIN\"]', '$2y$12$WpNxiqta17uM96iAeTcnf.WJ7fR5gkUNSeRLQ1WgQRDvJnrmAAbcC', 'Théo', 'Admin démo');
INSERT INTO `customer_request` (`id`, `first_name`, `last_name`, `email`, `phone`, `company`, `location`, `status`, `surface`, `description`, `date_request`, `user_id`, `reponse`, `montant_estime`) VALUES ('1', 'Théo', 'Démo', 'theo.client@example.test', NULL, NULL, '10 rue du Chantier, 75000 Ville Exemple', 'Nouvelle', '50', 'Préparer le terrain et apporter des graviers.', '2026-11-20', '1', NULL, '138000');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('1', '1');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('10', '1');
INSERT INTO `customer_request` (`id`, `first_name`, `last_name`, `email`, `phone`, `company`, `location`, `status`, `surface`, `description`, `date_request`, `user_id`, `reponse`, `montant_estime`) VALUES ('2', 'Théo', 'Démo', 'theo.client@example.test', NULL, NULL, '10 rue du Chantier, 75000 Ville Exemple', 'Acceptée', '50', 'Préparer le terrain pour une piscine de démonstration.', '2026-11-20', '1', 'Votre demande est acceptée. Nous vous contacterons pour confirmer le chantier.', '150000');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('7', '2');
INSERT INTO `customer_request` (`id`, `first_name`, `last_name`, `email`, `phone`, `company`, `location`, `status`, `surface`, `description`, `date_request`, `user_id`, `reponse`, `montant_estime`) VALUES ('3', 'Théo', 'Démo', 'theo.client@example.test', NULL, NULL, '10 rue du Chantier, 75000 Ville Exemple', 'En cours', '50', 'Évacuer les terres et niveler le terrain.', '2026-11-20', '1', 'La préparation de votre chantier est en cours.', '68000');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('8', '3');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('11', '3');
INSERT INTO `customer_request` (`id`, `first_name`, `last_name`, `email`, `phone`, `company`, `location`, `status`, `surface`, `description`, `date_request`, `user_id`, `reponse`, `montant_estime`) VALUES ('4', 'Théo', 'Démo', 'theo.client@example.test', NULL, NULL, '10 rue du Chantier, 75000 Ville Exemple', 'Terminée', '50', 'Créer une allée pavée de démonstration.', '2026-11-20', '1', 'Le chantier de démonstration est terminé.', '60000');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('9', '4');
INSERT INTO `customer_request` (`id`, `first_name`, `last_name`, `email`, `phone`, `company`, `location`, `status`, `surface`, `description`, `date_request`, `user_id`, `reponse`, `montant_estime`) VALUES ('5', 'Théo', 'Démo', 'theo.client@example.test', NULL, NULL, '10 rue du Chantier, 75000 Ville Exemple', 'Refusée', '50', 'Prévoir des travaux d’assainissement.', '2026-11-20', '1', 'Nous ne sommes pas disponibles à la date demandée.', '85000');
INSERT INTO `service_customer_request` (`service_id`, `customer_request_id`) VALUES ('2', '5');
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES ('DoctrineMigrations\\Version20261016133755', '2026-10-17 20:00:00', '0');
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES ('DoctrineMigrations\\Version20261017200333', '2026-10-17 20:00:00', '0');
COMMIT;
