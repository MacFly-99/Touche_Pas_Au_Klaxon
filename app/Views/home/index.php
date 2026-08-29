<h1>Trajets disponibles</h1>

<?php if (empty($trips)): ?>
    <div class="alert alert-info">Aucnun trajet disponible pour le moment.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Départ</th>
                    <th>Date de départ</th>
                    <th>Arrivée</th>
                    <th>Date d'arrivée</th>
                    <th>Places disponibles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= htmlspecialchars($trip['departure_name']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($trip['departure_datetime'])) ?></td>
                        <td><?= htmlspecialchars($trip['arrival_name']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($trip['arrival_datetime'])) ?></td>
                        <td><?= $trip['available_seats'] ?></td>
                        <td>
                            <!-- Bouton Détails -->
                             <button type="button" class="btn btn-info btn-sm" 
                             data-bs-toggle="modal" 
                             data-bs-target="#detailsModal"
                             data-trip-id="<?= $trip['id'] ?>">
                             Détails
                            </button>
                            
                            <!-- Bouton Modifier (uniquement pour l'auteur) -->
                             <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $trip['user_id']): ?>
                                <a href="<?= BASE_URL ?>/trip/edit/<?= $trip['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <?php endif; ?>

                            <!-- Bouton Supprimer avec modale (auteur OU administrateur) -->
                             <?php 
                             $isAuthor = isset($_SESSION['user']) && $_SESSION['user']['id'] == $trip['user_id'];
                             $isAdmin = isset($_SESSION['user']) && $_SESSION['user']['is_admin'] == 1;
                             if ($isAuthor || $isAdmin): 
                             ?>
                             <button type="button" class="btn btn-danger btn-sm" 
                             data-bs-toggle="modal" 
                             data-bs-target="#deleteModal"
                             data-trip-id="<?= $trip['id'] ?>">
                             Supprimer
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Modal Détails -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailsModalLabel">Détails du trajet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- Le contenu sera chargé dynamiquement en AJAX -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce trajet ?</p>
                <p class="text-danger fw-bold">⚠️ Cette action ne peut pas être annulée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les boutons "Details"
    const detailButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#detailsModal"]');
    
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tripId = this.getAttribute('data-trip-id');
            const modalBody = document.getElementById('detailsModalBody');
            
            // Afficher le spinner de chargement
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Requête AJAX pour récupérer les détails
            fetch('<?= BASE_URL ?>/trip/details/' + tripId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        modalBody.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }
                    
                    // Afficher les détails dans la modale
                    modalBody.innerHTML = `
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Auteur:</div>
                            <div class="col-8">${data.author_first_name} ${data.author_last_name}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Téléphone:</div>
                            <div class="col-8">${data.author_phone}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Email:</div>
                            <div class="col-8">${data.author_email}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Agence départ:</div>
                            <div class="col-8">${data.departure_name}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Date et heure départ:</div>
                            <div class="col-8">${new Date(data.departure_datetime).toLocaleString('fr-FR')}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Agence arrivée:</div>
                            <div class="col-8">${data.arrival_name}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Date et heure arrivée:</div>
                            <div class="col-8">${new Date(data.arrival_datetime).toLocaleString('fr-FR')}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Places totales:</div>
                            <div class="col-8">${data.total_seats}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4 fw-bold">Places disponibles:</div>
                            <div class="col-8">${data.available_seats}</div>
                        </div>
                    `;
                })
                .catch(error => {
                    modalBody.innerHTML = `<div class="alert alert-danger">Error loading trip details.</div>`;
                    console.error('Error:', error);
                });
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les boutons "Delete" qui ouvrent la modale
    const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#deleteModal"]');
    const deleteForm = document.getElementById('deleteForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Récupérer l'ID du trajet depuis l'attribut data-trip-id
            const tripId = this.getAttribute('data-trip-id');
            // Mettre à jour l'action du formulaire avec l'ID du trajet
            deleteForm.action = '<?= BASE_URL ?>/trip/delete/' + tripId;
        });
    });
});
</script>