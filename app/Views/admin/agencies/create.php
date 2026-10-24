<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4 mt-5">
            
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4 border-0">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-building-add fs-3 text-primary"></i>
                </div>
                <h4 class="mb-0 fw-bold">Nouvelle agence</h4>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="<?= BASE_URL ?>/admin/agencyStore">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom de l'agence" required>
                        <label for="name"><i class="bi bi-shop me-1 text-muted"></i>Nom de l'agence</label>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/admin/agencies" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>