<?php 
$title = 'Accueil'; 
ob_start(); 
?>

<div class="bg-light p-5 rounded">
    <h1 class="display-4">Bienvenue sur Mon Application</h1>
    <p class="lead">Cette structure de projet PHP personnalisée inclut tout ce dont vous avez besoin pour construire une application web robuste.</p>
    <hr class="my-4">
    <p>Commencez à développer votre application dès maintenant.</p>
    <?php if (!auth()): ?>
        <div class="d-flex gap-2">
            <a href="<?= url('/login') ?>" class="btn btn-primary">Se connecter</a>
            <a href="<?= url('/register') ?>" class="btn btn-outline-primary">S'inscrire</a>
        </div>
    <?php endif; ?>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Authentification</h5>
                <p class="card-text">Système d'authentification complet avec inscription, connexion, réinitialisation de mot de passe et authentification sociale.</p>
                <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle text-success"></i> Inscription et connexion</li>
                    <li><i class="bi bi-check-circle text-success"></i> Récupération de mot de passe</li>
                    <li><i class="bi bi-check-circle text-success"></i> Authentification Google et Facebook</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Routage</h5>
                <p class="card-text">Système de routage simple et efficace pour organiser les URLs de votre application.</p>
                <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle text-success"></i> Routes GET, POST, PUT, DELETE</li>
                    <li><i class="bi bi-check-circle text-success"></i> Réécriture d'URL avec .htaccess</li>
                    <li><i class="bi bi-check-circle text-success"></i> Contrôleurs organisés</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Base de données</h5>
                <p class="card-text">Interface simple pour interagir avec votre base de données et créer des modèles.</p>
                <ul class="list-unstyled">
                    <li><i class="bi bi-check-circle text-success"></i> ORM simple avec modèles</li>
                    <li><i class="bi bi-check-circle text-success"></i> Migration des tables</li>
                    <li><i class="bi bi-check-circle text-success"></i> Configuration multi-environnement</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
