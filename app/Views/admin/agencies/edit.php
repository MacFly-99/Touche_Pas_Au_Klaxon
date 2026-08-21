<h1>Edit agency</h1>

<form method="POST" action="<?= BASE_URL ?>/admin/agencyUpdate/<?= $agency['id'] ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Agency name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($agency['name']) ?>" required>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/agencies" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>