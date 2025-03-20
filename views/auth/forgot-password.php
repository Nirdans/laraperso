<?php 
$title = 'Mot de passe oublié'; 
ob_start(); 
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Réinitialisation du mot de passe</div>
            <div class="card-body">
                <p class="card-text">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
                
                <form action="<?= url('/forgot-password') ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">Envoyer le lien</button>
                        <a href="<?= url('/login') ?>" class="text-decoration-none">Retour à la connexion</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
