<h1>Create agency</h1>

<form method="POST" action="<?= BASE_URL ?>/admin/agencyStore">
    <div class="mb-3">
        <label for="name" class="form-label">Agency name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/agencies" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create</button>
    </div>
</form>