<?php 
$title = 'Gestion des utilisateurs'; 
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Utilisateurs</h1>
    <a href="<?= url('/users/create') ?>" class="btn btn-primary">Nouvel utilisateur</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date de création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun utilisateur trouvé</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user['id']) ?></td>
                            <td><?= e($user['name']) ?></td>
                            <td><?= e($user['email']) ?></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($user['created_at']))) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= url('/users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                    
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form action="<?= url('/users/' . $user['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean(); 
require BASE_PATH . '/views/layouts/main.php';
?>
