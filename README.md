# AQUARTZ — Terrassement

Projet de BTS SIO SLAM réalisé en binôme avec Symfony, Doctrine, Twig et MySQL.

## Tester avec la base fournie

La base complète de démonstration est dans [`bdd/aquartz_demo.sql`](bdd/aquartz_demo.sql). Les étapes d’import, les comptes client/admin et les parcours de test sont décrits dans [`bdd/README.md`](bdd/README.md). Cette installation permet de tester directement le site avec des données fictives.

## Installation locale

PHP 8.4.1 ou supérieur, Composer, MySQL et les extensions PHP habituelles de Symfony sont nécessaires. Utiliser PHP 8.5.10 dans le WAMP de ce projet.

Depuis le dossier `skeleton` :

```powershell
composer install
Copy-Item .env.example .env
```

Adapter la connexion MySQL dans `.env.local` et y définir une vraie valeur pour `APP_SECRET`. Ne pas écraser un fichier `.env` déjà configuré. Démarrer MySQL, puis :

```powershell
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate
symfony server:start --no-tls
```

Créer un compte depuis Inscription. Les comptes reçoivent seulement le rôle client. Pour un compte administrateur local, ajouter `ROLE_ADMIN` dans le tableau JSON `roles` de son utilisateur via phpMyAdmin, sans changer son mot de passe.

## Fonctionnement

- Un client peut choisir plusieurs prestations, voir leur total estimatif, envoyer une demande et consulter le statut ainsi que la réponse du professionnel.
- Un administrateur est dirigé vers son tableau de bord. Il peut traiter les demandes et créer, modifier ou supprimer des prestations. Une prestation utilisée dans une demande ou un projet est conservée.
- Les prix sont des forfaits indicatifs de démonstration, modifiables dans Gestion des prestations. Ils sont stockés en centimes. Le total est recalculé côté serveur puis conservé avec chaque nouvelle demande. Les anciennes demandes sans montant restent sur devis.
- Les réponses sont consultables dans l’espace client. Aucun e-mail n’est envoyé.
- Les images proposées dans le formulaire admin viennent de `public/images`. Leurs sources sont conservées dans `SOURCES-PHOTOS.txt`.

## Vérifications

Depuis `skeleton` :

```powershell
php bin/phpunit
php bin/console lint:twig templates
php bin/console lint:yaml config
php bin/console lint:container
php bin/console doctrine:schema:validate
node tests/verifier-total.cjs
node tests/verifier-carrousel.cjs
```

Les tests PHPUnit utilisent exclusivement une base SQLite locale dans `var/admin-tests.db`, recréée à chaque test. Activer `pdo_sqlite` pour les exécuter. Ils ne modifient pas `exemplebdd`.
