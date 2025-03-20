<?php 
$title = 'Page non trouvée'; 
ob_start(); 
?>

<div class="text-center py-5">
    <h1 class="display-1">404</h1>
    <p class="lead">Page non trouvée</p>
    <p>La page que vous recherchez n'existe pas ou a été déplacée.</p>
    <a href="<?= url('/home') ?>" class="btn btn-primary mt-3">Retourner à l'accueil</a>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
