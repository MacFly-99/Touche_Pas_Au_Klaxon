<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h1 class="mb-4">Modifier un trajet</h1>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/trip/update/<?= $trip['id'] ?>">
            <!-- Informations de l'utilisateur (non modifiables) -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Vos informations</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails du trajet -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Détails du trajet</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Départ -->
                        <div class="col-md-6 mb-3">
                            <label for="departure_agency" class="form-label">Agence de départ *</label>
                            <select class="form-select" id="departure_agency" name="departure_agency" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>" 
                                        <?= (isset($old['departure_agency']) && $old['departure_agency'] == $agency['id']) 
                                            || (!isset($old) && $trip['departure_agency_id'] == $agency['id']) 
                                            ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($agency['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Arrivée -->
                        <div class="col-md-6 mb-3">
                            <label for="arrival_agency" class="form-label">Agence d'arrivée *</label>
                            <select class="form-select" id="arrival_agency" name="arrival_agency" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($agencies as $agency): ?>
                                    <option value="<?= $agency['id'] ?>"
                                        <?= (isset($old['arrival_agency']) && $old['arrival_agency'] == $agency['id']) 
                                            || (!isset($old) && $trip['arrival_agency_id'] == $agency['id']) 
                                            ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($agency['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date et heure de départ -->
                        <div class="col-md-6 mb-3">
                            <label for="departure_datetime" class="form-label">Date de départ *</label>
                            <input type="date" class="form-control" id="departure_datetime" name="departure_datetime" 
                                   value="<?= isset($old['departure_datetime']) ? htmlspecialchars($old['departure_datetime']) : date('Y-m-d', strtotime($trip['departure_datetime'])) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="departure_time" class="form-label">Heure de départ *</label>
                            <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                   value="<?= isset($old['departure_time']) ? htmlspecialchars($old['departure_time']) : date('H:i', strtotime($trip['departure_datetime'])) ?>" required>
                        </div>

                        <!-- Date et heure d'arrivée -->
                        <div class="col-md-6 mb-3">
                            <label for="arrival_datetime" class="form-label">Date d'arrivée *</label>
                            <input type="date" class="form-control" id="arrival_datetime" name="arrival_datetime" 
                                   value="<?= isset($old['arrival_datetime']) ? htmlspecialchars($old['arrival_datetime']) : date('Y-m-d', strtotime($trip['arrival_datetime'])) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="arrival_time" class="form-label">Heure d'arrivé *</label>
                            <input type="time" class="form-control" id="arrival_time" name="arrival_time" 
                                   value="<?= isset($old['arrival_time']) ? htmlspecialchars($old['arrival_time']) : date('H:i', strtotime($trip['arrival_datetime'])) ?>" required>
                        </div>

                        <!-- Nombre de places -->
                        <div class="col-md-6 mb-3">
                            <label for="total_seats" class="form-label">Places totales *</label>
                            <input type="number" class="form-control" id="total_seats" name="total_seats" 
                                   min="1" value="<?= isset($old['total_seats']) ? htmlspecialchars($old['total_seats']) : $trip['total_seats'] ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>/" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Modifier le trajet</button>
            </div>
        </form>
    </div>
</div>