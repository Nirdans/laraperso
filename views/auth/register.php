<?php 
$title = 'Inscription'; 
ob_start(); 
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Inscription</div>
            <div class="card-body">
                <form action="<?= url('/register') ?>" method="post">
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
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                        <a href="<?= url('/login') ?>" class="text-decoration-none">Déjà inscrit?</a>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p>Ou inscrivez-vous avec:</p>
                    <a href="<?= url('/auth/google') ?>" class="btn btn-outline-danger me-2">
                        <i class="bi bi-google"></i> Google
                    </a>
                    <a href="<?= url('/auth/facebook') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
