<h1>Available trips</h1>

<?php if (empty($trips)): ?>
    <div class="alert alert-info">No trips available at the moment.</div>
<?php else: ?>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Departure</th>
                <th>Departure date</th>
                <th>Arrival</th>
                <th>Arrival date</th>
                <th>Available seats</th>
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