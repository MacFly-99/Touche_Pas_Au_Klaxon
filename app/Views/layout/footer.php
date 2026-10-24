</div> <!-- Fermeture du container ouvert dans le header -->

<footer class="mt-5 py-4" style="background-color: #00497c; border-top: 4px solid #0074c7;">
    <div class="container">
        <div class="row">
            <!-- Colonne 1 : Logo / Présentation -->
            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 45px; height: 45px; overflow: hidden;">
                        <img src="<?= BASE_URL ?>/assets/img/Logo.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    Touche Pas Au Klaxon
                </h5>
                <p class="text-white-50 small mb-0">
                    La solution de covoiturage interne pour faciliter vos déplacements professionnels au sein de l'entreprise.
                </p>
            </div>

            <!-- Colonne 2 : Liens rapides -->
            <div class="col-md-4 mb-3 mb-md-0">
                <h6 class="text-white text-uppercase fw-bold mb-3">Navigation</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="<?= BASE_URL ?>/" class="text-white-50 text-decoration-none hover-white">
                            <i class="bi bi-chevron-right small me-1"></i>Accueil
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>/trip/create" class="text-white-50 text-decoration-none hover-white">
                                <i class="bi bi-chevron-right small me-1"></i>Proposer un trajet
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Colonne 3 : Informations -->
            <div class="col-md-4">
                <h6 class="text-white text-uppercase fw-bold mb-3">Informations</h6>
                <p class="text-white-50 small mb-1">
                    <i class="bi bi-code-slash me-2"></i>Développé en MVC PHP
                </p>
                <p class="text-white-50 small mb-0">
                    <i class="bi bi-person-badge me-2"></i>Projet de MacFly-99
                </p>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <!-- Copyright -->
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="text-white-50 small mb-0">
                    &copy; <?= date('Y') ?> - Touche Pas Au Klaxon. Tous droits réservés. Usage interne uniquement.
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Petit effet de survol pour les liens du footer */
    .hover-white:hover {
        color: #ffffff !important;
        transition: color 0.2s ease-in-out;
    }
</style>
</body>
</html>