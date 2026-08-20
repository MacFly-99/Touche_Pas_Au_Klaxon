<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Trip;
use App\Models\Agency;

class TripController extends Controller
{
    // Affiche le formulaire de création
    public function create()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        $agencyModel = new Agency();
        $agencies = $agencyModel->findAll();

        $this->render('trip/create', [
            'agencies' => $agencies,
            'user' => $_SESSION['user']
        ]);
    }

    // Traite le formulaire (insertion en base)
    public function store()
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        // Récupération des données du formulaire
        $departureAgency = (int)$_POST['departure_agency'];
        $arrivalAgency = (int)$_POST['arrival_agency'];
        $departureDatetime = $_POST['departure_datetime'] . ' ' . $_POST['departure_time'];
        $arrivalDatetime = $_POST['arrival_datetime'] . ' ' . $_POST['arrival_time'];
        $totalSeats = (int)$_POST['total_seats'];

        // --- Validation ---
        $errors = [];

        // 1. Départ et arrivée doivent être différents
        if ($departureAgency === $arrivalAgency) {
            $errors[] = "Departure and arrival agencies must be different.";
        }

        // 2. La date d'arrivée doit être postérieure à la date de départ
        if (strtotime($departureDatetime) >= strtotime($arrivalDatetime)) {
            $errors[] = "Arrival date/time must be after departure date/time.";
        }

        // 3. Le nombre de places doit être > 0
        if ($totalSeats < 1) {
            $errors[] = "Total seats must be at least 1.";
        }

        // 4. Vérifier que la date de départ n'est pas dans le passé
        if (strtotime($departureDatetime) < time()) {
            $errors[] = "Departure date/time cannot be in the past.";
        }

        // Si des erreurs, on retourne au formulaire avec les messages
        if (!empty($errors)) {
            $agencyModel = new Agency();
            $agencies = $agencyModel->findAll();
            $this->render('trip/create', [
                'agencies' => $agencies,
                'user' => $_SESSION['user'],
                'errors' => $errors,
                'old' => $_POST // pour pré-remplir les champs
            ]);
            return;
        }

        // --- Insertion en base ---
        $tripModel = new Trip();
        $data = [
            'departure_agency_id' => $departureAgency,
            'arrival_agency_id' => $arrivalAgency,
            'departure_datetime' => $departureDatetime,
            'arrival_datetime' => $arrivalDatetime,
            'total_seats' => $totalSeats,
            'user_id' => $_SESSION['user']['id']
        ];

        if ($tripModel->insert($data)) {
            $_SESSION['flash']['success'] = "Trip created successfully!";
        } else {
            $_SESSION['flash']['error'] = "Error creating trip. Please try again.";
        }

        $this->redirect('/');
    }

    public function details($id)
    {
        $tripModel = new Trip();
        $trip = $tripModel->findById($id);
        
        if ($trip) {
            // Retourne les données en JSON
            header('Content-Type: application/json');
            echo json_encode($trip);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trip not found']);
        }
        exit;
    }
}

?>