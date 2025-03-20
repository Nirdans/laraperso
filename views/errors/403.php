<?php 
$title = 'Accès Interdit'; 
ob_start(); 
?>

<div class="text-center py-5">
    <h1 class="display-1">403</h1>
    <p class="lead">Accès interdit</p>
    <p>Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.</p>
    <a href="<?= url('/home') ?>" class="btn btn-primary mt-3">Retourner à l'accueil</a>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
