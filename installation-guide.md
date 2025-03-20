# Guide d'installation du framework



# Installation 
- composer create-project --prefer-dist --stability=dev nirdans/laraperso nom-du-projet


## Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Composer (https://getcomposer.org/)
- Git

## Étapes d'installation

### 1. Cloner le dépôt GitHub
```bash
cd /opt/lampp/htdocs
git clone https://github.com/Nirdans/laraperso.git
cd laraperso
```

### 2. Installer les dépendances via Composer
```bash
composer install
```

### 3. Créer et configurer le fichier de configuration
```bash
cp config/config.example.php config/config.php
```
Ensuite, modifiez le fichier `config/config.php` avec vos paramètres de base de données et autres configurations.

### 4. Exécuter le script d'installation
```bash
php install.php
```

### 5. Configurer les permissions des dossiers (si nécessaire)
```bash
chmod -R 775 cache/
chmod -R 775 logs/
chmod -R 775 assets/uploads/
```

### 6. Accéder à l'application
Votre application est maintenant accessible via:
```
http://localhost/laraperso
```

## Commandes utiles

### Exécuter le serveur de développement intégré
```bash
php dos serve
```

### Créer une migration
```bash
php dos migrate:create nom_de_la_table
```

### Exécuter les migrations
```bash
php dos migrate
```

## Remarque de sécurité
N'oubliez pas de supprimer le fichier `install.php` après l'installation initiale pour des raisons de sécurité.

## Support
Pour plus d'informations, visitez [www.sandrindossou.com](https://www.sandrindossou.com)
