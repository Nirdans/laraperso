# Framework PHP Personnalisé

Un framework PHP léger et personnalisé avec authentification, routage, et structure MVC.

## Fonctionnalités

- **Authentification complète** : Inscription, connexion, déconnexion, récupération de mot de passe
- **Authentification sociale** : Intégration avec Google et Facebook
- **Système de routage** : Routes GET, POST, PUT, DELETE facilement configurables
- **Structure MVC** : Organisation claire du code suivant le modèle MVC
- **ORM simple** : Interface élégante pour interagir avec la base de données
- **Sécurité renforcée** : Protection CSRF, échappement des données, protection contre les injections SQL
- **Configuration flexible** : Environnements de développement et de production

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extension PDO PHP activée
- Serveur web (Apache, Nginx, etc.) avec mod_rewrite activé

## Installation

1. Clonez ou téléchargez ce dépôt dans votre répertoire web
2. Configurez votre serveur web pour pointer vers le répertoire du projet
3. Configurez la base de données dans le fichier `config/config.php`
4. Exécutez `install.php` pour initialiser la base de données
5. Supprimez `install.php` après l'installation

## Structure du projet

```
/
├── app/                  # Code de l'application
│   ├── controllers/      # Contrôleurs
│   ├── core/             # Classes fondamentales du framework
│   ├── models/           # Modèles de données
│   └── services/         # Services (DB, Auth, etc.)
├── assets/               # Ressources web
│   ├── css/              # Feuilles de styles
│   ├── js/               # Scripts JavaScript
│   ├── img/              # Images
│   └── uploads/          # Fichiers téléchargés
├── bootstrap/            # Fichiers de démarrage
├── cache/                # Fichiers mis en cache
├── config/               # Configuration
├── helpers/              # Fonctions utilitaires
├── logs/                 # Journaux
├── routes/               # Définitions des routes
├── views/                # Vues de l'application
│   ├── auth/             # Vues d'authentification
│   ├── errors/           # Pages d'erreur
│   ├── layouts/          # Layouts des pages
│   └── users/            # Vues de gestion utilisateurs
├── .htaccess             # Configuration Apache
├── index.php             # Point d'entrée
└── install.php           # Script d'installation
```

## Utilisation

### Définir une nouvelle route

Ouvrez le fichier `routes/web.php` et ajoutez une nouvelle route :

```php
Router::get('/mon-chemin', 'MonController@maMethode');
```

### Créer un contrôleur

Créez un nouveau fichier dans `app/controllers/` :

```php
<?php
namespace App\Controllers;

class MonController
{
    public function maMethode()
    {
        // Votre code ici
        require BASE_PATH . '/views/ma-vue.php';
    }
}
```

### Créer un modèle

Créez un nouveau fichier dans `app/models/` :

```php
<?php
namespace App\Models;

class MonModele extends Model
{
    protected $table = 'ma_table';
    protected $fillable = ['champ1', 'champ2', 'champ3'];
    
    // Méthodes personnalisées
}
```

### Créer une vue

Créez un nouveau fichier dans `views/` :

```php
<?php 
$title = 'Mon Titre'; 
ob_start(); 
?>

<h1>Ma Vue</h1>
<p>Mon contenu</p>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
```

## Licence

Ce framework est distribué sous licence MIT. Vous êtes libre de l'utiliser, le modifier et le distribuer.

## Crédits

Développé comme un outil personnel pour accélérer le développement de projets PHP. By Sandrin DOSSOU
