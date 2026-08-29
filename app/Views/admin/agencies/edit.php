<h1>Modifier une agence</h1>

<form method="POST" action="<?= BASE_URL ?>/admin/agencyUpdate/<?= $agency['id'] ?>">
    <div class="mb-3">
        <label for="name" class="form-label">Nom de l'agence</label>
        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($agency['name']) ?>" required>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/agencies" class="btn btn-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </div>
</form>