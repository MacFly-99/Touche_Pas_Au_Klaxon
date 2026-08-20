<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don't touch the horn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-color: #f1f8fc;
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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00497c;">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/">Don't touch the horn</a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                    <ul class="navbar-nav me-3">
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/users">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/agencies">Agencies</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/trips">Trips</a></li>
                    </ul>
                    <span class="navbar-text text-light me-3">
                        Hello <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
                    </span>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/trip/create" class="btn btn-outline-light me-2">Offer a trip</a>
                    <span class="navbar-text text-light me-3">
                        <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
                    </span>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-danger">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary">Login</a>
            <?php endif; ?>
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