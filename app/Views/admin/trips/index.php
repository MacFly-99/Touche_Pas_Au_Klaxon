<h1>Tous les Trajets (côté admin)</h1>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Départ</th>
                <th>Arrivée</th>
                <th>Jour et heure de départ</th>
                <th>Jour et heure d'arrivée</th>
                <th>Places totales</th>
                <th>Places disponibles</th>
                <th>Auteur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?= $trip['id'] ?></td>
                    <td><?= htmlspecialchars($trip['departure_name']) ?></td>
                    <td><?= htmlspecialchars($trip['arrival_name']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($trip['departure_datetime'])) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($trip['arrival_datetime'])) ?></td>
                    <td><?= $trip['total_seats'] ?></td>
                    <td><?= $trip['available_seats'] ?></td>
                    <td><?= htmlspecialchars($trip['author_first_name'] . ' ' . $trip['author_last_name']) ?></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteTripModal"
                        data-trip-id="<?= $trip['id'] ?>">
                        Supprimer
                        </button>
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal de confirmation suppression trajet (admin) -->
    <div class="modal fade" id="deleteTripModal" tabindex="-1" aria-labelledby="deleteTripModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteTripModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce trajet ?</p>
                    <p class="text-danger fw-bold">⚠️ Cette action ne peut pas être annulée.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteTripForm" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#deleteTripModal"]');
    const deleteForm = document.getElementById('deleteTripForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tripId = this.getAttribute('data-trip-id');
            deleteForm.action = '<?= BASE_URL ?>/admin/tripDelete/' + tripId;
        });
    });
});
</script>