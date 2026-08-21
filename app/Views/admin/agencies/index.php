<h1>Agencies management</h1>

<a href="<?= BASE_URL ?>/admin/agencyCreate" class="btn btn-primary mb-3">Add agency</a>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($agencies as $agency): ?>
                <tr>
                    <td><?= $agency['id'] ?></td>
                    <td><?= htmlspecialchars($agency['name']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/admin/agencyEdit/<?= $agency['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button type="button" class="btn btn-danger btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteAgencyModal"
                        data-agency-id="<?= $agency['id'] ?>"
                        data-agency-name="<?= htmlspecialchars($agency['name']) ?>">
                        Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal de confirmation suppression agence -->
    <div class="modal fade" id="deleteAgencyModal" tabindex="-1" aria-labelledby="deleteAgencyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteAgencyModalLabel">Confirm deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the agency <strong id="agencyNameDisplay"></strong>?</p>
                    <p class="text-danger fw-bold">⚠️ This action cannot be undone.</p>
                    <p>All trips associated with this agency will also be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteAgencyForm" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#deleteAgencyModal"]');
    const deleteForm = document.getElementById('deleteAgencyForm');
    const agencyNameDisplay = document.getElementById('agencyNameDisplay');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const agencyId = this.getAttribute('data-agency-id');
            const agencyName = this.getAttribute('data-agency-name');
            deleteForm.action = '<?= BASE_URL ?>/admin/agencyDelete/' + agencyId;
            if (agencyNameDisplay) {
                agencyNameDisplay.textContent = agencyName;
            }
        });
    });
});
</script>