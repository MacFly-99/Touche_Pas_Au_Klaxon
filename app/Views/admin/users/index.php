<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 mt-3">
    <h2 class="h3 mb-0 text-secondary fw-bold">Gestion des utilisateurs</h2>
    <a href="<?= BASE_URL ?>/admin/userCreate" class="btn btn-primary shadow-sm w-100 w-md-auto text-nowrap">
        <i class="bi bi-person-plus-fill me-1"></i> Créer un utilisateur
    </a>
</div>

<div class="table-responsive bg-white rounded-3 shadow-sm p-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($users ?? []) as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><strong><?= htmlspecialchars($user['last_name']) ?></strong></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone']) ?></td>
                    <td>
                        <?php if ($user['is_admin']): ?>
                            <span class="badge bg-danger"><i class="bi bi-shield-lock me-1"></i>Admin</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark"><i class="bi bi-person me-1"></i>Utilisateur</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?php 
                        $isSelf = isset($_SESSION['user']['id']) && $user['id'] == $_SESSION['user']['id']; 
                        ?>

                        <?php if ($isSelf): ?>
                            <button class="btn btn-secondary btn-sm" disabled title="Vous ne pouvez pas modifier votre propre compte.">
                                <i class="bi bi-slash-circle"></i> Protégé
                            </button>
                        <?php else: ?>
                            
                            <!-- CONTENEUR FLEX : Colonne sur mobile, Ligne sur PC -->
                            <div class="d-flex flex-column flex-md-row gap-2 justify-content-end">
                                
                                <!-- Bouton qui ouvre la modale de changement de rôle -->
                                <?php if ($user['is_admin']): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#roleModal"
                                            data-user-id="<?= $user['id'] ?>"
                                            data-user-name="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>"
                                            data-action="rétrograder"
                                            data-btn-class="btn-secondary">
                                        <i class="bi bi-arrow-down-circle"></i> Rétrograder
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-outline-success btn-sm w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#roleModal"
                                            data-user-id="<?= $user['id'] ?>"
                                            data-user-name="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>"
                                            data-action="promouvoir"
                                            data-btn-class="btn-success">
                                        <i class="bi bi-arrow-up-circle"></i> Promouvoir
                                    </button>
                                <?php endif; ?>

                                <!-- Bouton Supprimer -->
                                <button type="button" 
                                        class="btn btn-danger btn-sm w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteUserModal"
                                        data-user-id="<?= $user['id'] ?>"
                                        data-user-name="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                                
                            </div>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- MODALE 1 : Changement de rôle              -->
<!-- ========================================== -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="roleModalLabel">Changer le rôle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir <strong id="roleActionName"></strong> l'utilisateur <strong id="roleUserName"></strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="roleForm" method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" id="roleSubmitBtn" class="btn">Confirmer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODALE 2 : Suppression d'un utilisateur    -->
<!-- ========================================== -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="deleteUserName"></strong> ?</p>
                <p class="text-danger fw-bold">⚠️ Cette action suspendra le compte (Soft Delete).</p>
                <p>Les trajets associés à cet utilisateur seront conservés.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteUserForm" method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS POUR LES MODALES                   -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- SCRIPT POUR LA MODALE DE CHANGEMENT DE RÔLE ---
    var roleModal = document.getElementById('roleModal');
    roleModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');
        var action = button.getAttribute('data-action');
        var btnClass = button.getAttribute('data-btn-class');
        
        roleModal.querySelector('#roleUserName').textContent = userName;
        roleModal.querySelector('#roleActionName').textContent = action;
        
        var form = roleModal.querySelector('#roleForm');
        form.action = '<?= BASE_URL ?>/admin/userToggleRole/' + userId;

        var submitBtn = roleModal.querySelector('#roleSubmitBtn');
        submitBtn.className = 'btn ' + btnClass;
        submitBtn.textContent = (action === 'promouvoir') ? 'Promouvoir' : 'Rétrograder';
    });

    // --- SCRIPT POUR LA MODALE DE SUPPRESSION ---
    var deleteUserModal = document.getElementById('deleteUserModal');
    deleteUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');
        
        var modalBodyInput = deleteUserModal.querySelector('#deleteUserName');
        modalBodyInput.innerHTML = userName + '&nbsp;?';
        
        var form = deleteUserModal.querySelector('#deleteUserForm');
        form.action = '<?= BASE_URL ?>/admin/userDelete/' + userId;
    });

});
</script>