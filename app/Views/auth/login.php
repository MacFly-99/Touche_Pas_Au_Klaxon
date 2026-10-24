<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-6 col-lg-4">
        
        <!-- Carte de connexion avec ombre portée -->
        <div class="card shadow-lg border-0 rounded-4">
            
            <!-- En-tête de la carte -->
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4 border-0">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-lock fs-2 text-primary"></i>
                </div>
                <h4 class="mb-0 fw-bold">Connexion</h4>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <!-- Message d'erreur -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/auth/login">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    
                    <!-- Champ Email avec icône -->
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control bg-light" id="email" name="email" placeholder="nom@exemple.com" required>
                        <label for="email"><i class="bi bi-envelope me-2 text-muted"></i>Adresse Email</label>
                    </div>
                    
                    <!-- Champ Mot de passe avec icône -->
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control bg-light" id="password" name="password" placeholder="Mot de passe" required>
                        <label for="password"><i class="bi bi-key me-2 text-muted"></i>Mot de passe</label>
                    </div>
                    
                    <!-- Bouton de connexion -->
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                        Se connecter <i class="bi bi-box-arrow-in-right ms-2"></i>
                    </button>
                </form>
                
            </div>
        </div>
        
        <!-- Petit texte sous la carte -->
        <p class="text-center text-muted mt-4 small">
            Accès réservé aux collaborateurs de l'entreprise.
        </p>

    </div>
</div>