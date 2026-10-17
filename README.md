# AQUARTZ — Terrassement

Site pour une entreprise de terrassement, réalisé en binôme en BTS SIO SLAM à Saint-Adjutor. Les clients consultent les prestations et envoient une demande. Le professionnel suit les demandes et leur répond depuis son espace administrateur.

## Tester avec la base fournie

La base complète de démonstration est dans [`bdd/aquartz_demo.sql`](bdd/aquartz_demo.sql). Les étapes d’import, les comptes client/admin et les parcours de test sont décrits dans [`bdd/README.md`](bdd/README.md). Cette installation permet de tester directement le site avec des données fictives.

## Installation locale

PHP 8.4.1 ou supérieur, Composer, MySQL et les extensions PHP habituelles de Symfony sont nécessaires. Utiliser PHP 8.5.10 dans le WAMP de ce projet.

Depuis le dossier `skeleton` :

```powershell
Copy-Item .env.example .env
composer install
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

Les tests sont dans `skeleton/tests`. Pour ajouter un test unitaire, créer une classe dont le nom termine par `Test`, héritant de `TestCase`, comme `PrixTest.php`. Pour tester une page ou un formulaire, utiliser `WebTestCase`, comme dans `AdminTest.php`. Vérifier le résultat attendu avec une assertion et ajouter un cas invalide quand il est utile. Les scripts JavaScript utilisent les assertions de Node.js.

## Auteurs

- Théo Boursier — étudiant en BTS SIO SLAM à Saint-Adjutor.
- Enki Robert — étudiant en BTS SIO SLAM à Saint-Adjutor.

## Technologies

- [Symfony 8.1](https://symfony.com/doc/8.1/index.html) et [PHP](https://www.php.net/manual/fr/) pour le fonctionnement du site.
- [Doctrine ORM](https://www.doctrine-project.org/projects/doctrine-orm/en/current/index.html) pour les entités et les données.
- [Twig](https://twig.symfony.com/doc/3.x/) pour les pages HTML.
- [MySQL](https://dev.mysql.com/doc/) pour la base de données.
- HTML, CSS et JavaScript pour la présentation et les interactions.
- PHPUnit et Node.js pour les tests.

Les versions des dépendances PHP sont enregistrées dans `skeleton/composer.lock`. Symfony CLI est utilisé pour lancer le serveur local. Node.js est nécessaire uniquement pour les scripts de test JavaScript. Activer notamment `pdo_mysql`, `ctype`, `iconv` et les extensions demandées par Composer.

## Guide et conventions

- Garder les noms des entités et des variables existantes.
- Utiliser des noms français pour les nouvelles routes et pages.
- Ranger les pages dans `templates`, les formulaires dans `src/Form` et les contrôleurs dans `src/Controller`.
- Utiliser un CSS par page, avec `base.css` et `Components.css` pour les éléments communs.
- Écrire des commentaires courts, avec une majuscule au début.
- Garder les paramètres personnels dans `.env.local`, qui ne doit pas être envoyé sur GitHub.

## API

Le site ne consomme pas d’API externe pour ses fonctionnalités. Les données sont gérées dans la base MySQL du projet.

## Compilation et lancement

Aucune compilation du CSS ou du JavaScript n’est nécessaire : ces fichiers sont dans `skeleton/public`. Composer installe les dépendances PHP. Le lancement local se fait avec `symfony server:start --no-tls` depuis `skeleton`.

Ouvrir l’adresse affichée par Symfony CLI, généralement http://127.0.0.1:8000. Après l’import de la base de démonstration, ne pas recréer les tables : suivre les étapes du guide dans `bdd/README.md`.

## Déploiement

Le rendu est disponible sur la branche `main`. L’installation locale est décrite plus haut et dans le guide de la base de démonstration. Aucun hébergement public n’est configuré dans ce dépôt. Pour un hébergement PHP, la racine web doit pointer vers `skeleton/public`, avec la connexion MySQL adaptée et les paramètres personnels hors de Git.

## Contribution et versionnement

Le suivi des tâches se fait sur [Trello](https://trello.com/b/xWqGnmMl/site-aquartz). Le code est partagé sur [GitHub](https://github.com/kidondrvgs/prj-aquartz).

Créer une branche `feature/nom-de-la-tache` à partir de `dev`, faire les modifications et les vérifications, puis créer un commit et pousser la branche. Fusionner ensuite dans `dev` avec `git merge --no-ff`. La version finale est fusionnée de `dev` vers `main` pour le rendu. Se coordonner avant de modifier les fichiers de son binôme.

## Licence et crédits

Le projet est déclaré `proprietary` dans Composer. Aucune licence libre de réutilisation du code n’est accordée dans ce dépôt.

L’interface suit les maquettes du projet. Les photos ajoutées pour les prestations viennent de Pexels et restent soumises à leur licence. Les liens et crédits sont dans [SOURCES-PHOTOS.txt](skeleton/public/images/SOURCES-PHOTOS.txt). Ce sont des illustrations, pas des photos de chantiers réalisés par AQUARTZ.
