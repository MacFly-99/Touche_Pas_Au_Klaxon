<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touche Pas Au Klaxon</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00497c;">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Don't touch the horn</a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                    <ul class="navbar-nav me-3">
                        <li class="nav-item"><a class="nav-link" href="/admin/users">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/agencies">Agencies</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/trips">Trips</a></li>
                    </ul>
                    <span class="navbar-text text-light me-3">
                        Hello <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
                    </span>
                <?php else: ?>
                    <a href="/trip/create" class="btn btn-outline-light me-2">Offer a trip</a>
                    <span class="navbar-text text-light me-3">
                        <?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>
                    </span>
                <?php endif; ?>
                <a href="/auth/logout" class="btn btn-danger">Logout</a>
            <?php else: ?>
                <a href="/auth/login" class="btn btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container mt-4">