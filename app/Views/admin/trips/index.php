<h1>All trips (admin view)</h1>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure datetime</th>
                <th>Arrival datetime</th>
                <th>Seats total</th>
                <th>Seats avail.</th>
                <th>Author</th>
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
                        Delete
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
                    <h5 class="modal-title" id="deleteTripModalLabel">Confirm deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this trip?</p>
                    <p class="text-danger fw-bold">⚠️ This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteTripForm" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-danger">Delete</button>
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