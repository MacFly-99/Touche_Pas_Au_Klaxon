<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/auth/login">
                <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Sign in</button>
                </form>

                <div class="mt-3 text-center text-muted small">
                    <p>Test user: alexandre.martin@email.fr<br>Password: <strong>secret</strong></p>
                    <p>Admin: admin@covoiturage.fr<br>Password: <strong>secret</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>