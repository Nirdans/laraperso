<?php 
$title = 'Créer un utilisateur'; 
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Créer un utilisateur</h1>
    <a href="<?= url('/users') ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('/users') ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required autofocus>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
