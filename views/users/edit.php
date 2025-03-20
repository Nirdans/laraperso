<?php 
$title = 'Modifier un utilisateur'; 
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Modifier l'utilisateur</h1>
    <a href="<?= url('/users') ?>" class="btn btn-secondary">Retour</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= url('/users/' . $user['id']) ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= old('name', $user['name']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= old('email', $user['email']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe (laissez vide pour conserver)</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="form-text text-muted">Laissez ce champ vide si vous ne souhaitez pas changer le mot de passe.</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
