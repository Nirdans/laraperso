<?php 
$title = 'Profil utilisateur'; 
ob_start(); 
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-body text-center">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?= e($user['profile_image']) ?>" alt="Photo de profil" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <img src="<?= asset('img/default-avatar.png') ?>" alt="Photo de profil" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php endif; ?>
                <h5 class="card-title mb-0"><?= e($user['name']) ?></h5>
                <div class="text-muted mb-2"><?= e($user['email']) ?></div>
                <div class="small text-muted">
                    Membre depuis le <?= e(date('d/m/Y', strtotime($user['created_at']))) ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($user['google_id']) || !empty($user['facebook_id'])): ?>
            <div class="card mb-4">
                <div class="card-header">Comptes liés</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($user['google_id'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-google text-danger me-2"></i> Google
                                </div>
                                <span class="badge bg-success">Lié</span>
                            </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['facebook_id'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-facebook text-primary me-2"></i> Facebook
                                </div>
                                <span class="badge bg-success">Lié</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">Modifier le profil</div>
            <div class="card-body">
                <form action="<?= url('/profile') ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                    </div>
                    
                    <hr>
                    <h5>Changer le mot de passe</h5>
                    <p class="text-muted small">Laissez ces champs vides si vous ne souhaitez pas changer votre mot de passe</p>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
