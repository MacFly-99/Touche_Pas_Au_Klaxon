<!-- BANNIÈRE (HERO) -->
<div class="p-5 mb-4 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #00497c, #0074c7);">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold mb-2">Où allez-vous aujourd'hui ?</h1>
        <p class="fs-5 mb-0">Trouvez le covoiturage parfait pour vos déplacements professionnels.</p>
    </div>
</div>

<!-- TITRE DE SECTION -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0 text-secondary fw-bold">Trajets disponibles</h2>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="<?= BASE_URL ?>/trip/create" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Proposer un trajet
        </a>
    <?php endif; ?>
</div>

<!-- GRILLE DE CARTES -->
<?php if (empty($trips)): ?>
    <div class="alert alert-info shadow-sm border-0 d-flex align-items-center" role="alert">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>Aucun trajet disponible pour le moment. Revenez plus tard ou proposez le vôtre !</div>
    </div>
<?php else: ?>
    <div class="row g-4 mb-5">
        <?php foreach ($trips as $trip): ?>
            <div class="col-md-6 col-lg-4">
                <!-- position-relative est obligatoire pour que le stretched-link fonctionne -->
                <div class="card h-100 shadow-sm border-0 hover-card position-relative">
                    
                    <!-- En-tête de la carte -->
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark border"><i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($trip['departure_datetime'])) ?></span>
                        
                        <!-- Badge des places dispo -->
                        <?php if ($trip['available_seats'] > 0): ?>
                            <span class="badge bg-success rounded-pill"><i class="bi bi-person-check me-1"></i> <?= $trip['available_seats'] ?> place(s)</span>
                        <?php else: ?>
                            <span class="badge bg-danger rounded-pill"><i class="bi bi-x-circle me-1"></i> Complet</span>
                        <?php endif; ?>
                    </div>

                    <!-- Corps de la carte -->
                    <div class="card-body">
                        <!-- Le lien invisible qui rend TOUTE la carte cliquable pour ouvrir les détails -->
                        <a href="#" class="stretched-link" 
                           data-bs-toggle="modal" 
                           data-bs-target="#detailsModal"
                           data-trip-id="<?= $trip['id'] ?>">
                        </a>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Départ à <?= date('H:i', strtotime($trip['departure_datetime'])) ?></small>
                                <strong class="fs-5 text-dark"><?= htmlspecialchars($trip['departure_name']) ?></strong>
                            </div>
                        </div>

                        <div class="border-start border-2 border-primary ms-3 ps-4 py-1 mb-3"></div>

                        <div class="d-flex align-items-center">
                            <div class="bg-dark text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-geo-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Arrivée à <?= date('H:i', strtotime($trip['arrival_datetime'])) ?></small>
                                <strong class="fs-5 text-dark"><?= htmlspecialchars($trip['arrival_name']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Pied de la carte (Actions) -->
                    <!-- z-index: 2 permet de garder les boutons cliquables au-dessus du stretched-link -->
                    <div class="card-footer bg-white border-top-0 pb-4 pt-0 d-flex justify-content-between align-items-center" style="position: relative; z-index: 2;">
                        <span class="text-primary fw-bold small"><i class="bi bi-search"></i> Détails</span>
                        
                        <div class="d-flex gap-2">
                            <!-- Bouton Modifier (uniquement pour l'auteur) -->
                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $trip['user_id']): ?>
                                <a href="<?= BASE_URL ?>/trip/edit/<?= $trip['id'] ?>" class="btn btn-warning btn-sm" title="Modifier">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
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
                                        data-trip-id="<?= $trip['id'] ?>" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- MODALES ET SCRIPTS JS -->
<!-- ========================================== -->

<!-- Modal Détails -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailsModalLabel">Détails du trajet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce trajet ?</p>
                <p class="text-danger fw-bold">⚠️ Cette action ne peut pas être annulée.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- SCRIPT POUR LA MODALE DÉTAILS ---
    // On écoute l'événement d'ouverture de la modale
    var detailsModal = document.getElementById('detailsModal');
    detailsModal.addEventListener('show.bs.modal', function (event) {
        // Le bouton (ou la carte) qui a déclenché la modale
        var triggerElement = event.relatedTarget;
        var tripId = triggerElement.getAttribute('data-trip-id');
        var modalBody = document.getElementById('detailsModalBody');
        
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
                    <hr>
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
                    <hr>
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
                modalBody.innerHTML = `<div class="alert alert-danger">Erreur lors du chargement des détails.</div>`;
                console.error('Error:', error);
            });
    });

    // --- SCRIPT POUR LA MODALE DE SUPPRESSION ---
    var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var tripId = button.getAttribute('data-trip-id');
        
        var form = deleteModal.querySelector('#deleteForm');
        form.action = '<?= BASE_URL ?>/trip/delete/' + tripId;
    });
});
</script>