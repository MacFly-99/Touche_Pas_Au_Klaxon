<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touche Pas Au Klaxon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --bs-primary: #0074c7;
            --bs-secondary: #384050;
            --bs-success: #82b864;
            --bs-danger: #cd2c2e;
            --bs-light: #f1f8fc;
            --bs-dark: #00497c;
        }
        .btn-primary {
            background-color: #0074c7;
            border-color: #0074c7;
        }
        .btn-primary:hover {
            background-color: #00497c;
            border-color: #00497c;
        }
        .btn-danger {
            background-color: #cd2c2e;
            border-color: #cd2c2e;
        }
        .navbar-dark {
            background-color: #00497c !important;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f1f8fc;
        }
        body > .container {
            flex: 1;
        }
        .bg-primary {
            background-color: #0074c7 !important;
        }
        .bg-dark {
            background-color: #00497c !important;
        }
        .text-primary {
            color: #0074c7 !important;
        }
        .hover-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            cursor: pointer;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00497c;">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/">Touche Pas Au Klaxon</a>
        
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user'])): ?>
                
                <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                    <!-- Menu Admin -->
                    <ul class="navbar-nav me-3">
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/users">Utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/agencies">Agences</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/trips">Trajets</a></li>
                    </ul>
                    <span class="navbar-text text-light me-3">
                        <i class="bi bi-shield-lock me-1"></i> Admin: <?= htmlspecialchars($_SESSION['user']['first_name']) ?>
                    </span>
                <?php else: ?>
                    <!-- Menu Utilisateur -->
                    <a href="<?= BASE_URL ?>/trip/create" class="btn btn-outline-light btn-sm me-3">
                        <i class="bi bi-plus-circle me-1"></i> Proposer un trajet
                    </a>
                    <span class="navbar-text text-light me-3">
                        <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
                    </span>
                <?php endif; ?>
                
                <!-- Bouton Déconnexion (pour tout le monde) -->
                <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                </a>

            <?php endif; ?>
            <!-- Note : on ne met rien pour les visiteurs, car ils sont sur la page de login -->
        </div>
    </div>
</nav>
<div class="container mt-4">
    <?php if (isset($_SESSION['flash']['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash']['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']['error']); ?>
<?php endif; ?>