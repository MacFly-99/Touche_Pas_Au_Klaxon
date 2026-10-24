<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-4 mt-4">
            
            <!-- En-tête -->
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4 border-0">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-plus-fill fs-3 text-primary"></i>
                </div>
                <h4 class="mb-0 fw-bold">Créer un utilisateur</h4>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <!-- Affichage des erreurs -->
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0 small">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/admin/userStore">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    
                    <!-- Infos personnelles -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" placeholder="Nom" required>
                                <label for="last_name">Nom *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" placeholder="Prénom" required>
                                <label for="first_name">Prénom *</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="Email" required>
                                <label for="email">Adresse Email *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="Téléphone" required>
                                <label for="phone">Téléphone *</label>
                            </div>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required minlength="6">
                        <label for="password">Mot de passe * (6 caractères min.)</label>
                    </div>

                    <!-- Rôle (Admin ou User) -->
                    <div class="form-floating mb-4">
                        <select class="form-select" id="is_admin" name="is_admin">
                            <option value="0" <?= (isset($old['is_admin']) && $old['is_admin'] == 0) ? 'selected' : '' ?>>Utilisateur standard</option>
                            <option value="1" <?= (isset($old['is_admin']) && $old['is_admin'] == 1) ? 'selected' : '' ?>>Administrateur</option>
                        </select>
                        <label for="is_admin">Rôle de l'utilisateur</label>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Créer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>