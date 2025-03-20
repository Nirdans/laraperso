# Guide d'installation du framework Laraperso

## Méthode 1: Installation directe depuis GitHub

```bash
# Étape 1: Cloner le dépôt GitHub
git clone https://github.com/Nirdans/laraperso.git nom-du-projet

# Étape 2: Se déplacer dans le répertoire du projet
cd nom-du-projet

# Étape 3: Installer les dépendances avec Composer
composer install
```

## Méthode 2: Installation avec Composer en utilisant un dépôt VCS

Créez d'abord un fichier `composer.json` dans votre répertoire de destination:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Nirdans/laraperso"
        }
    ],
    "require": {
        "nirdans/laraperso": "dev-main"
    }
}
```

Puis exécutez:

```bash
composer install
```

## Méthode 3: Installation via ZIP

```bash
# Étape 1: Télécharger l'archive ZIP du projet
wget https://github.com/Nirdans/laraperso/archive/refs/heads/main.zip -O laraperso.zip

# Étape 2: Extraire l'archive
unzip laraperso.zip -d nom-du-projet

# Étape 3: Se déplacer dans le répertoire du projet
cd nom-du-projet

# Étape 4: Installer les dépendances avec Composer
composer install
```

## Configuration post-installation

1. Renommez `config/config.example.php` en `config/config.php`
2. Modifiez les paramètres de base de données dans `config/config.php`
3. Exécutez le script d'installation: `php install.php`

## Problèmes courants

### Erreur "Could not find package nirdans/laraperso with stability dev"

Cette erreur survient lors de l'utilisation de `composer create-project` car le package n'est pas publié sur Packagist.org. Utilisez l'une des méthodes alternatives décrites ci-dessus.

### Erreur "The requested package nirdans/laraperso could not be found"

Assurez-vous d'avoir correctement configuré le dépôt VCS dans votre `composer.json` comme indiqué dans la méthode 2.

## Support

Pour toute assistance technique supplémentaire, visitez [www.sandrindossou.com](https://www.sandrindossou.com/).
