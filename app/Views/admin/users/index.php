<h1>Liste des utilisateurs</h1>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($users ?? []) as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td><?= $user['is_admin'] ? 'Oui' : 'Non' ?></td>
                    <td>
                        <?php 
                        // On vérifie si l'utilisateur de la ligne est celui qui est connecté
                        $isSelf = isset($_SESSION['user']['id']) && $user['id'] == $_SESSION['user']['id']; 
                        ?>

                        <?php if ($isSelf): ?>
                            <!-- Bouton désactivé pour soi-même -->
                            <button class="btn btn-secondary btn-sm" disabled title="Vous ne pouvez pas supprimer votre propre compte.">
                                Supprimer
                            </button>
                        <?php else: ?>
                            <!-- Bouton normal qui ouvre la modale -->
                            <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteUserModal"
                                    data-user-id="<?= $user['id'] ?>"
                                    data-user-name="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>">
                                Supprimer
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal de suppression d'un utilisateur -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="deleteUserName"></strong> ?</p>
                <p class="text-danger fw-bold">⚠️ Cette action ne peut être annulée.</p>
                <p>Les trajets associés à cet utilisateur seront conservés.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteUserForm" method="POST" style="display:inline;">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Le script magique -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var deleteUserModal = document.getElementById('deleteUserModal');
    deleteUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');
        
        var modalBodyInput = deleteUserModal.querySelector('#deleteUserName');
        // On ajoute un espace insécable avant le ? pour éviter qu'il passe à la ligne tout seul
        modalBodyInput.innerHTML = userName + '&nbsp;?';
        
        var form = deleteUserModal.querySelector('#deleteUserForm');
        form.action = '<?= BASE_URL ?>/admin/userDelete/' + userId;
    });
});
</script>