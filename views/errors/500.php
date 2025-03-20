<?php 
$title = 'Erreur Serveur'; 
ob_start(); 
?>

<div class="text-center py-5">
    <h1 class="display-1">500</h1>
    <p class="lead">Erreur interne du serveur</p>
    <p>Une erreur s'est produite lors du traitement de votre requête.</p>
    <p class="text-muted">
        <?php if(defined('ENVIRONMENT') && ENVIRONMENT === 'development' && isset($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>
    </p>
    <a href="<?= url('/home') ?>" class="btn btn-primary mt-3">Retourner à l'accueil</a>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
