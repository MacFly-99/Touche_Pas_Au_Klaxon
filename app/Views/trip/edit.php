<?php
if (!isset($trip) || !is_array($trip)) {
    $trip = [];
}
if (!isset($agencies) || !is_array($agencies)) {
    $agencies = [];
}
if (!isset($user) || !is_array($user)) {
    $user = [
        'first_name' => $trip['first_name'] ?? '',
        'last_name'  => $trip['last_name'] ?? '',
        'email'      => $trip['email'] ?? '',
        'phone'      => $trip['phone'] ?? '',
    ];
}
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        
        <!-- Carte principale -->
        <div class="card shadow-lg border-0 rounded-4 mt-4">
            
            <!-- En-tête de la carte -->
            <div class="card-header bg-warning text-dark text-center py-4 rounded-top-4 border-0">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-pencil-square fs-3 text-warning"></i>
                </div>
                <h3 class="mb-0 fw-bold">Modifier le trajet</h3>
                <p class="mb-0 text-dark-50 small mt-1">Mettez à jour les détails de votre itinéraire</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                
                <!-- Affichage des erreurs -->
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs :</h6>
                        <ul class="mb-0 small">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/trip/update/<?= $trip['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                    <!-- INFOS UTILISATEUR (Rappel) -->
                    <div class="bg-light p-3 rounded-3 mb-4 border">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <i class="bi bi-person-circle fs-2 text-secondary"></i>
                            </div>
                            <div class="col">
                                <small class="text-muted d-block">Auteur du trajet :</small>
                                <strong class="text-dark"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></strong>
                                <span class="text-muted small ms-2">(<?= htmlspecialchars($user['email'] ?? '') ?>)</span>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 1 : ITINÉRAIRE -->
                    <h5 class="fw-bold text-secondary mb-3 border-bottom pb-2">
                        <i class="bi bi-geo-alt me-2 text-primary"></i>Itinéraire
                    </h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="form-floating">
                                <select class="form-select" id="departure_agency" name="departure_agency" required>
                                    <option value="" disabled <?= !isset($old['departure_agency']) ? 'selected' : '' ?>>Choisir...</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= $agency['id'] ?>" 
                                            <?= (isset($old['departure_agency']) && $old['departure_agency'] == $agency['id']) 
                                                || (!isset($old) && $trip['departure_agency_id'] == $agency['id']) 
                                                ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($agency['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="departure_agency"><i class="bi bi-geo-alt-fill text-primary me-1"></i>Agence de départ *</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="arrival_agency" name="arrival_agency" required>
                                    <option value="" disabled <?= !isset($old['arrival_agency']) ? 'selected' : '' ?>>Choisir...</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= $agency['id'] ?>"
                                            <?= (isset($old['arrival_agency']) && $old['arrival_agency'] == $agency['id']) 
                                                || (!isset($old) && $trip['arrival_agency_id'] == $agency['id']) 
                                                ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($agency['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="arrival_agency"><i class="bi bi-geo-fill text-dark me-1"></i>Agence d'arrivée *</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2 : DATES ET HEURES -->
                    <h5 class="fw-bold text-secondary mb-3 mt-4 border-bottom pb-2">
                        <i class="bi bi-calendar-event me-2 text-primary"></i>Dates & Heures
                    </h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="departure_datetime" name="departure_datetime" 
                                       value="<?= isset($old['departure_datetime']) ? htmlspecialchars($old['departure_datetime']) : date('Y-m-d', strtotime($trip['departure_datetime'])) ?>" required>
                                <label for="departure_datetime">Date de départ *</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                       value="<?= isset($old['departure_time']) ? htmlspecialchars($old['departure_time']) : date('H:i', strtotime($trip['departure_datetime'])) ?>" required>
                                <label for="departure_time">Heure de départ *</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="arrival_datetime" name="arrival_datetime" 
                                       value="<?= isset($old['arrival_datetime']) ? htmlspecialchars($old['arrival_datetime']) : date('Y-m-d', strtotime($trip['arrival_datetime'])) ?>" required>
                                <label for="arrival_datetime">Date d'arrivée *</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="time" class="form-control" id="arrival_time" name="arrival_time" 
                                       value="<?= isset($old['arrival_time']) ? htmlspecialchars($old['arrival_time']) : date('H:i', strtotime($trip['arrival_datetime'])) ?>" required>
                                <label for="arrival_time">Heure d'arrivée *</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3 : PLACES -->
                    <h5 class="fw-bold text-secondary mb-3 mt-4 border-bottom pb-2">
                        <i class="bi bi-people me-2 text-primary"></i>Capacité
                    </h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="total_seats" name="total_seats" 
                                       min="1" value="<?= isset($old['total_seats']) ? htmlspecialchars($old['total_seats']) : $trip['total_seats'] ?>" required>
                                <label for="total_seats"><i class="bi bi-person-plus me-1"></i>Places disponibles *</label>
                            </div>
                        </div>
                    </div>

                    <!-- BOUTONS D'ACTION -->
                    <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                        <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm text-dark">
                            <i class="bi bi-check-circle me-1"></i> Enregistrer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>