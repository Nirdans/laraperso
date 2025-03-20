# Framework PHP Personnalisé

Un framework PHP léger et moderne pour le développement d'applications web.

## Structure du Projet

```
/
├── app/                    # Coeur de l'application
│   ├── Commands/          # Commandes CLI
│   │   ├── Make.php       # Générateur de code
│   │   └── Migrate.php    # Gestion des migrations
│   ├── Controllers/       # Contrôleurs
│   ├── Core/             # Classes principales
│   │   ├── App.php       # Class principale
│   │   ├── Auth.php      # Authentification
│   │   ├── Controller.php # Contrôleur de base
│   │   ├── Database.php  # Connexion base de données
│   │   ├── Model.php     # Modèle de base
│   │   ├── Request.php   # Gestion des requêtes
│   │   ├── Response.php  # Gestion des réponses
│   │   ├── Router.php    # Routage
│   │   ├── Session.php   # Gestion des sessions
│   │   └── View.php      # Gestion des vues
│   ├── Middleware/       # Middlewares
│   │   ├── Auth.php      # Authentification
│   │   └── Guest.php     # Accès invité 
│   ├── Models/           # Modèles
│   └── Services/         # Services
│       ├── Mail.php      # Service d'emails
│       └── Upload.php    # Gestion des uploads
│
├── bootstrap/             # Démarrage application
│   └── app.php           # Bootstrap principal
│
├── config/               # Configuration
│   ├── config.php       # Configuration principale
│   └── config.example.php # Example de configuration
│
├── database/             # Base de données
│   ├── migrations/      # Fichiers de migration
│   └── seeds/          # Données de test
│
├── helpers/              # Fonctions helper
│   ├── assets.php       # Gestion des assets
│   ├── auth.php         # Helpers authentification
│   ├── form.php         # Helpers formulaires
│   └── functions.php    # Fonctions générales
│
├── public/              # Dossier public
│   ├── assets/         # Assets statiques
│   │   ├── css/       # Fichiers CSS
│   │   ├── js/        # Fichiers JavaScript
│   │   └── img/       # Images
│   ├── uploads/        # Fichiers uploadés
│   ├── .htaccess      # Configuration Apache
│   └── index.php      # Point d'entrée
│
├── routes/              # Routes
│   ├── api.php        # Routes API
│   └── web.php        # Routes Web
│
├── views/              # Vues de l'application
│   ├── layouts/       # Layouts principaux
│   ├── pages/         # Pages
│   ├── auth/          # Authentication
│   ├── admin/         # Administration
│   ├── errors/        # Pages d'erreur
│   └── components/    # Composants réutilisables
│
├── storage/            # Stockage
│   ├── app/           # Fichiers application
│   ├── logs/          # Logs
│   └── cache/         # Cache
│
├── tests/             # Tests unitaires
├── vendor/            # Dépendances Composer
├── .env              # Variables d'environnement
├── .gitignore        # Fichiers ignorés par Git
├── composer.json     # Dépendances du projet
├── dos              # CLI du framework
├── LICENSE          # Licence du projet
└── README.md        # Documentation
```

## Fonctionnalités Principales

- **Routage**: Système de routage simple avec support des méthodes HTTP
- **MVC**: Architecture Model-View-Controller
- **Base de données**: ORM simple avec migrations
- **Authentification**: Support connexion/inscription + auth sociale
- **Upload**: Gestion des fichiers avec validation
- **Sécurité**: Protection CSRF, XSS, SQL injection
- **Validation**: Validation des données avec messages d'erreur
- **Cache**: Système de cache simple
- **Mail**: Support d'envoi d'emails via SMTP

## Configuration

1. Copiez `config/config.example.php` vers `config/config.php`
2. Configurez votre base de données et autres paramètres
3. Créez la base de données
4. Exécutez les migrations : `php console migrate`

## Prérequis

- PHP 7.4+
- MySQL 5.7+
- Extensions PHP requises:
  - PDO
  - mbstring
  - xml
  - curl
  - gd

## Installation

```bash
# Cloner le projet
git clone [url-du-repo]

# Installer les dépendances
composer install

# Configurer l'environnement
cp config/config.example.php config/config.php

# Créer la base de données et exécuter les migrations
php dos migrate
```

## Migration de la Base de Données

Pour gérer vos migrations, utilisez les commandes suivantes dans le terminal :

```bash
# Exécuter toutes les migrations en attente
php dos migrate

# Annuler la dernière migration
php dos migrate:rollback

# Réinitialiser toutes les migrations
php dos migrate:reset

# Réinitialiser et réexécuter toutes les migrations
php dos migrate:refresh

# Créer une nouvelle migration
php dos make:migration create_users_table
```

Les commandes disponibles :
- `migrate` : Exécute toutes les migrations en attente
- `migrate:rollback` : Annule la dernière migration
- `migrate:reset` : Annule toutes les migrations
- `migrate:refresh` : Réinitialise et réexécute toutes les migrations
- `make:migration` : Crée un nouveau fichier de migration

## Documentation

Pour plus de détails sur l'utilisation du framework, consultez les sections suivantes:

- [Guide de démarrage](docs/getting-started.md)
- [Routing](docs/routing.md)
- [Base de données](docs/database.md)
- [Authentification](docs/auth.md)
- [Validation](docs/validation.md)

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## Auteur

Framework développé par [Sandrin DOSSOU](https://www.sandrindossou.com)
