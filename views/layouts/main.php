<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mon Application' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= time() ?>">->
    <!-- Styles intégrés pour tester -->nk rel="stylesheet" type="text/css" href="<?= asset('css/style.css') ?>?v=<?= time() ?>">
    <style>-- Ajout d'une version pour éviter la mise en cache -->
        body {
            padding-bottom: 70px;
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            display: flex;
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
                        <a class="nav-link" href="<?= url('/users') ?>">Utilisateurs</a>res */
                    </li>back {
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (auth()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">125rem 0.25rem rgba(0, 0, 0, 0.075);
                            <?= e($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('/profile') ?>">Profil</a></li>
                            <li><hr class="dropdown-divider"></li> #4e73df;
                            <li><a class="dropdown-item" href="<?= url('/logout') ?>">Déconnexion</a></li> #4e73df;
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">lor: #2e59d9;
                        <a class="nav-link" href="<?= url('/login') ?>">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/register') ?>">Inscription</a>
                    </li>
                    <?php endif; ?>vbar navbar-expand-lg navbar-dark bg-primary">
                </ul>lass="container">
            <a class="navbar-brand" href="<?= url('/') ?>">Mon Application</a>  </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">        </div>
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">') ?>
                    <li class="nav-item"><?= flash_message('error') ?>
                        <a class="nav-link" href="<?= url('/home') ?>">Accueil</a>() ?>
                    </li>
                    <?php if (auth()): ?>        <?= $content ?? '' ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/users') ?>">Utilisateurs</a>
                    </li>
                    <?php endif; ?>lass="container text-center">
                </ul>p class="mb-0">&copy; <?= date('Y') ?> Mon Application. Tous droits réservés.</p>
                <ul class="navbar-nav">        </div>
                    <?php if (auth()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">ript src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
                            <?= e($_SESSION['user_name']) ?>ript src="<?= asset('js/app.js') ?>"></script>
                        </a></body>






































</html></body>    <script src="<?= asset('js/app.js') ?>"></script>    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>    </footer>        </div>            <p class="mb-0">&copy; <?= date('Y') ?> Mon Application. Tous droits réservés.</p>        <div class="container text-center">    <footer class="bg-light py-4 mt-5">    </div>        <?= $content ?? '' ?>                <?= validation_errors() ?>        <?= flash_message('error') ?>        <?= flash_message('success') ?>    <div class="container mt-4">    </nav>        </div>            </div>                </ul>                    <?php endif; ?>                    </li>                        <a class="nav-link" href="<?= url('/register') ?>">Inscription</a>                    <li class="nav-item">                    </li>                        <a class="nav-link" href="<?= url('/login') ?>">Connexion</a>                    <li class="nav-item">                    <?php else: ?>                    </li>                        </ul>                            <li><a class="dropdown-item" href="<?= url('/logout') ?>">Déconnexion</a></li>                            <li><hr class="dropdown-divider"></li>                            <li><a class="dropdown-item" href="<?= url('/profile') ?>">Profil</a></li>                        <ul class="dropdown-menu dropdown-menu-end"></html>
