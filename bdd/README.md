# Base de démonstration AQUARTZ

Le fichier `aquartz_demo.sql` contient la structure complète et uniquement des données fictives : 12 prestations avec prix indicatifs, un compte client, un compte admin et cinq demandes avec les différents statuts. Les demandes acceptées, en cours, terminées et refusées ont une réponse de démonstration.

## Importer avec phpMyAdmin

1. Démarrer MySQL dans WAMP et ouvrir phpMyAdmin.
2. Créer une base **vide** appelée `aquartz_demo`, avec le jeu de caractères `utf8mb4`.
3. Sélectionner cette base, ouvrir **Importer** et choisir `aquartz_demo.sql`.
4. Importer le fichier une seule fois. Il ne supprime aucune table existante : ne pas l’importer dans la base de travail.

L’export est préparé et vérifié avec MySQL 9.1. MySQL 8 ou supérieur est conseillé. La compatibilité avec MariaDB n’a pas été vérifiée.

## Connecter le projet

Depuis `skeleton`, installer les dépendances avec `composer install`. Copier `.env.example` vers `.env` seulement si ce fichier n’existe pas encore.

Créer ou adapter `.env.local` :

```dotenv
APP_ENV=dev
APP_SECRET=une-valeur-aleatoire-pour-cette-installation-locale
DATABASE_URL="mysql://root:@127.0.0.1:3306/aquartz_demo?serverVersion=9.1.0&charset=utf8mb4"
```

Adapter l’utilisateur, le mot de passe, le port et la version à son installation MySQL. Le paramètre `serverVersion` doit correspondre au serveur utilisé. Les migrations déjà appliquées sont enregistrées dans l’export ; ne pas recréer les tables après l’import.

Lancer le site depuis `skeleton` :

```powershell
symfony server:start --no-tls
```

## Comptes de test

| Rôle | Adresse e-mail | Mot de passe |
| --- | --- | --- |
| Client | theo.client@example.test | Client123! |
| Administrateur | theo.admin@example.test | Admin123! |

Ces comptes et mots de passe sont publics et réservés à la démonstration locale. Les mots de passe sont hachés dans le fichier SQL. Aucun compte ni aucune demande personnelle n’a été exporté.

## Parcours à essayer

- Client : consulter ses cinq demandes, leurs statuts et les réponses du professionnel.
- Client : créer une demande avec plusieurs prestations et vérifier le total estimatif.
- Admin : consulter le tableau de bord, filtrer les demandes, changer un statut et enregistrer une réponse.
- Client : retrouver ensuite cette réponse dans le détail de sa demande.
- Admin : créer et modifier une prestation et son prix ; la suppression est refusée pour une prestation déjà utilisée.

Les tarifs sont indicatifs et destinés au projet scolaire. Aucun e-mail n’est envoyé. Les adresses en `example.test` ne sont pas des boîtes de messagerie réelles.
