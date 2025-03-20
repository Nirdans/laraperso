<?php 
$title = 'Connexion'; 
ob_start(); 
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Connexion</div>
            <div class="card-body">
                <form action="<?= url('/login') ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">Connexion</button>
                        <a href="<?= url('/forgot-password') ?>" class="text-decoration-none">Mot de passe oublié?</a>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p>Ou connectez-vous avec:</p>
                    <a href="<?= url('/auth/google') ?>" class="btn btn-outline-danger me-2">
                        <i class="bi bi-google"></i> Google
                    </a>
                    <a href="<?= url('/auth/facebook') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    
                    <p class="mt-3">Pas encore de compte? <a href="<?= url('/register') ?>" class="text-decoration-none">S'inscrire</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
