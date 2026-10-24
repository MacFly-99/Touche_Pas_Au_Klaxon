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
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #00497c;">
    <div class="container-fluid">
        <!-- Logo / Marque -->
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/">
            <i class="bi bi-car-front-fill me-1"></i> Touche Pas Au Klaxon
        </a>
        
        <!-- Bouton Burger (visible uniquement sur mobile) -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenu de la navbar (qui se cache sur mobile) -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
                <?php if (isset($_SESSION['user'])): ?>
                    
                    <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                        <!-- Menu Admin -->
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/users">Utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/agencies">Agences</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/trips">Trajets</a></li>
                        <li class="nav-item">
                            <span class="navbar-text text-light fw-bold bg-dark bg-opacity-25 px-3 py-1 rounded-pill">
                                <i class="bi bi-shield-lock me-1"></i> Admin: <?= htmlspecialchars($_SESSION['user']['first_name']) ?>
                            </span>
                        </li>
                    <?php else: ?>
                        <!-- Menu Utilisateur -->
                        <li class="nav-item">
                            <a href="<?= BASE_URL ?>/trip/create" class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-plus-circle me-1"></i> Proposer un trajet
                            </a>
                        </li>
                        <li class="nav-item">
                            <span class="navbar-text text-light fw-bold px-3">
                                <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['user']['first_name']) ?>
                            </span>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Bouton Déconnexion -->
                    <li class="nav-item w-100 text-center mt-2 mt-lg-0 w-auto">
                        <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-danger btn-sm px-4">
                            <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                        </a>
                    </li>

                <?php endif; ?>
            </ul>
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