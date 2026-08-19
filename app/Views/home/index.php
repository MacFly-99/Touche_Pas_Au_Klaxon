<h1>Trajets disponibles</h1>

<?php if (empty($trips)): ?>
    <div class="alert alert-info">Aucun trajet disponible pour le moment.</div>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Départ</th>
                <th>Date de départ</th>
                <th>Arrivée</th>
                <th>Date d'arrivée</th>
                <th>Places disponibles</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?= htmlspecialchars($trip['departure_name']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($trip['departure_datetime'])) ?></td>
                    <td><?= htmlspecialchars($trip['arrival_name']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($trip['arrival_datetime'])) ?></td>
                    <td><?= $trip['available_seats'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>