<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="card shadow-lg border-0 rounded-4 mt-4">
            
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4 border-0">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-gear fs-3 text-primary"></i>
                </div>
                <h4 class="mb-0 fw-bold">Mon Profil</h4>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <!-- Infos Utilisateur (Non modifiables ici) -->
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <?php
                        $profileUser = $user ?? ($_SESSION['user'] ?? []);
                        $profileName = trim(($profileUser['first_name'] ?? '') . ' ' . ($profileUser['last_name'] ?? ''));
                    ?>
                    <p class="mb-1"><strong>Nom :</strong> <?= htmlspecialchars($profileName !== '' ? $profileName : 'Non disponible') ?></p>
                    <p class="mb-0"><strong>Email :</strong> <?= htmlspecialchars($profileUser['email'] ?? 'Non disponible') ?></p>
                </div>

                <h5 class="fw-bold text-secondary mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>Changer de mot de passe
                </h5>

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

                <form method="POST" action="<?= BASE_URL ?>/profile/updatePassword">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Mot de passe actuel" required>
                        <label for="current_password">Mot de passe actuel *</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nouveau mot de passe" required minlength="6">
                        <label for="new_password">Nouveau mot de passe * (6 car. min.)</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required minlength="6">
                        <label for="confirm_password">Confirmer le nouveau mot de passe *</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Mettre à jour
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>