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

    // Affiche le formulaire de modification
    public function edit($id)
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);

        // Vérifier que le trajet existe
        if (!$trip) {
            $_SESSION['flash']['error'] = "Trip not found.";
            $this->redirect('/');
            return;
        }

        // Vérifier que l'utilisateur est l'auteur du trajet
        if ($trip['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "You are not authorized to edit this trip.";
            $this->redirect('/');
            return;
        }

        // Récupérer la liste des agences pour le formulaire
        $agencyModel = new Agency();
        $agencies = $agencyModel->findAll();

        $this->render('trip/edit', [
            'trip' => $trip,
            'agencies' => $agencies,
            'user' => $_SESSION['user']
        ]);
    }

    // Traite le formulaire de modification
    public function update($id)
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

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);

        // Vérifier que le trajet existe
        if (!$trip) {
            $_SESSION['flash']['error'] = "Trip not found.";
            $this->redirect('/');
            return;
        }

        // Vérifier que l'utilisateur est l'auteur
        if ($trip['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "You are not authorized to edit this trip.";
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
            $this->render('trip/edit', [
                'trip' => $trip,
                'agencies' => $agencies,
                'user' => $_SESSION['user'],
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        // --- Mise à jour en base ---
        $data = [
            'departure_agency_id' => $departureAgency,
            'arrival_agency_id' => $arrivalAgency,
            'departure_datetime' => $departureDatetime,
            'arrival_datetime' => $arrivalDatetime,
            'total_seats' => $totalSeats,
            'available_seats' => $totalSeats // on remet les places disponibles = total
        ];

        if ($tripModel->update($id, $data)) {
            $_SESSION['flash']['success'] = "Trip updated successfully!";
        } else {
            $_SESSION['flash']['error'] = "Error updating trip. Please try again.";
        }

        $this->redirect('/');
    }

    // Affiche une boîte de dialogue pour la suppression d'un trajet
    public function delete($id)
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

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);

        // Vérifier que le trajet existe
        if (!$trip) {
            $_SESSION['flash']['error'] = "Trip not found.";
            $this->redirect('/');
            return;
        }

        // Vérifier les droits : auteur OU administrateur
        $isAuthor = ($trip['user_id'] == $_SESSION['user']['id']);
        $isAdmin = isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == 1;

        if (!$isAuthor && !$isAdmin) {
            $_SESSION['flash']['error'] = "You are not authorized to delete this trip.";
            $this->redirect('/');
            return;
        }

        // Supprimer le trajet
        if ($tripModel->delete($id)) {
            $_SESSION['flash']['success'] = "Trip deleted successfully!";
        } else {
            $_SESSION['flash']['error'] = "Error deleting trip. Please try again.";
        }

        $this->redirect('/');
    }
}

?>