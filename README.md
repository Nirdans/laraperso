# Framework PHP Personnalisé

Un framework PHP léger et moderne pour le développement d'applications web.

## Structure du Projet

```
/
├── app/
│   ├── Commands/         # Commandes CLI (ex: migrations)
│   ├── Controllers/      # Contrôleurs de l'application
│   ├── Core/            # Classes principales du framework
│   ├── Middleware/      # Middlewares
│   ├── Models/          # Modèles de données
│   └── Services/        # Services (Auth, Mail, etc.)
│
├── assets/
│   ├── css/            # Fichiers CSS
│   ├── js/             # Fichiers JavaScript
│   ├── img/            # Images
│   └── uploads/        # Fichiers uploadés
│
├── bootstrap/
│   └── app.php         # Fichier d'amorçage
│
├── config/
│   ├── config.php      # Configuration principale
│   └── config.example.php  # Example de configuration
│
├── database/
│   ├── migrations/     # Fichiers de migration
│   └── seeds/          # Données de test
│
├── public/
│   └── index.php       # Point d'entrée
│
├── routes/
│   └── web.php         # Définition des routes
│
├── views/
│   ├── layouts/        # Templates de base
│   ├── auth/           # Vues d'authentification
│   ├── errors/         # Pages d'erreur
│   └── components/     # Composants réutilisables
│
└── vendor/             # Dépendances Composer
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
