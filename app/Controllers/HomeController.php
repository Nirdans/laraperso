<?php
namespace App\Controllers;

class HomeController
{
    public function __construct()
    {
        // Vérifier si l'utilisateur est connecté pour certaines pages
        // Dans ce cas, la page d'accueil peut être accessible sans connexion
    }
    
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        // Si l'utilisateur est connecté, on récupère ses informations
        if (auth()) {
            $user = current_user();
        }
        
        require BASE_PATH . '/views/home.php';
    }
    
    /**
     * Affiche une page a propos
     */
    public function about()
    {
        $title = 'À propos';
        
        ob_start();
        ?>
        <div class="card">
            <div class="card-body">
                <h1>À propos de ce framework</h1>
                <p class="lead">
                    Ce framework PHP personnalisé a été développé par <strong>Sandrin DOSSOU</strong> 
                    pour accélérer le développement d'applications web.
                </p>
                <p>
                    <strong>Caractéristiques principales :</strong>
                </p>
                <ul>
                    <li>Architecture MVC simple et intuitive</li>
                    <li>Système de routage flexible</li>
                    <li>Authentification complète et sécurisée</li>
                    <li>Gestion de base de données simplifiée</li>
                    <li>Flexible et extensible selon vos besoins</li>
                </ul>
                <p>
                    <a href="https://www.sandrindossou.com" target="_blank" class="btn btn-primary">
                        <i class="bi bi-globe"></i> Visiter mon site web
                    </a>
                </p>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        require BASE_PATH . '/views/layouts/main.php';
    }
}
