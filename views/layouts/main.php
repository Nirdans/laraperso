<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mon Application' ?> | Sandrin DOSSOU</title>
    <meta name="author" content="Sandrin DOSSOU - https://www.sandrindossou.com/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= time() ?>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= url('/') ?>">Mon Application</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/home') ?>">Accueil</a>
                    </li>
                    <?php if (auth()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/users') ?>">Utilisateurs</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (auth()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?= e($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('/profile') ?>">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/logout') ?>">Déconnexion</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/login') ?>">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/register') ?>">Inscription</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?= flash_message('success') ?>
        <?= flash_message('error') ?>
        <?= validation_errors() ?>
        
        <?= $content ?? '' ?>
    </div>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Mon Application. Tous droits réservés.</p>
            <p class="text-muted mt-2">
                Développé avec <i class="bi bi-heart-fill text-danger"></i> par 
                <a href="https://www.sandrindossou.com/" target="_blank" class="text-decoration-none">Sandrin DOSSOU</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
