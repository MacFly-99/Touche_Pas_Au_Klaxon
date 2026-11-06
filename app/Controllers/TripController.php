<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Trip;
use App\Models\Agency;

class TripController extends Controller
{
    public function create()
    {
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

    public function store()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $this->verifyCsrfToken();

        $departureAgency = (int)($_POST['departure_agency'] ?? 0);
        $arrivalAgency = (int)($_POST['arrival_agency'] ?? 0);
        $departureDate = $_POST['departure_datetime'] ?? '';
        $departureTime = $_POST['departure_time'] ?? '';
        $arrivalDate = $_POST['arrival_datetime'] ?? '';
        $arrivalTime = $_POST['arrival_time'] ?? '';
        $totalSeats = (int)($_POST['total_seats'] ?? 0);

        $departureDatetime = $departureDate . ' ' . $departureTime;
        $arrivalDatetime = $arrivalDate . ' ' . $arrivalTime;

        $errors = [];

        if ($departureAgency === $arrivalAgency) {
            $errors[] = "Les agences de départ et d'arrivée doivent être différentes.";
        }

        if (strtotime($departureDatetime) >= strtotime($arrivalDatetime)) {
            $errors[] = "La date d'arrivée doit être postérieure à la date de départ.";
        }

        if ($totalSeats < 1) {
            $errors[] = "Le nombre de places doit être d'au moins 1.";
        }

        if (strtotime($departureDatetime) < time()) {
            $errors[] = "La date de départ ne peut pas être dans le passé.";
        }

        if (!empty($errors)) {
            $agencyModel = new Agency();
            $agencies = $agencyModel->findAll();
            $this->render('trip/create', [
                'agencies' => $agencies,
                'user' => $_SESSION['user'],
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $tripModel = new Trip();
        $data = [
            'departure_agency_id' => $departureAgency,
            'arrival_agency_id' => $arrivalAgency,
            'departure_datetime' => $departureDatetime,
            'arrival_datetime' => $arrivalDatetime,
            'total_seats' => $totalSeats,
            'available_seats' => $totalSeats,
            'user_id' => $_SESSION['user']['id']
        ];

        if ($tripModel->insert($data)) {
            $_SESSION['flash']['success'] = "Trajet créé avec succès !";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la création du trajet.";
        }

        $this->redirect('/');
    }

    // Affiche les détails (Appel AJAX pour la modale)
    public function details($id)
    {
        $id = (int)$id; // Sécurité ID

        // SÉCURITÉ : On vérifie que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);
        
        if ($trip) {
            // On laisse le JS gérer l'échappement HTML, on envoie les données brutes
            header('Content-Type: application/json');
            echo json_encode($trip);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trajet introuvable']);
        }
        exit;
    }

    public function edit($id)
    {
        $id = (int)$id; // Sécurité ID
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);

        if (!$trip) {
            $_SESSION['flash']['error'] = "Trajet introuvable.";
            $this->redirect('/');
            return;
        }

        if ($trip['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "Vous n'êtes pas l'auteur de ce trajet.";
            $this->redirect('/');
            return;
        }

        $agencyModel = new Agency();
        $agencies = $agencyModel->findAll();

        $this->render('trip/edit', [
            'trip' => $trip,
            'agencies' => $agencies,
            'user' => $_SESSION['user']
        ]);
    }

    public function update($id)
    {
        $id = (int)$id; // Sécurité ID
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $this->verifyCsrfToken();

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);

        if (!$trip) {
            $_SESSION['flash']['error'] = "Trajet introuvable.";
            $this->redirect('/');
            return;
        }

        if ($trip['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "Vous n'êtes pas l'auteur de ce trajet.";
            $this->redirect('/');
            return;
        }

        $departureAgency = (int)($_POST['departure_agency'] ?? 0);
        $arrivalAgency = (int)($_POST['arrival_agency'] ?? 0);
        $departureDate = $_POST['departure_datetime'] ?? '';
        $departureTime = $_POST['departure_time'] ?? '';
        $arrivalDate = $_POST['arrival_datetime'] ?? '';
        $arrivalTime = $_POST['arrival_time'] ?? '';
        $totalSeats = (int)($_POST['total_seats'] ?? 0);

        $departureDatetime = $departureDate . ' ' . $departureTime;
        $arrivalDatetime = $arrivalDate . ' ' . $arrivalTime;

        $errors = [];

        if ($departureAgency === $arrivalAgency) {
            $errors[] = "Les agences de départ et d'arrivée doivent être différentes.";
        }

        if (strtotime($departureDatetime) >= strtotime($arrivalDatetime)) {
            $errors[] = "La date d'arrivée doit être postérieure à la date de départ.";
        }

        if ($totalSeats < 1) {
            $errors[] = "Le nombre de places doit être d'au moins 1.";
        }

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

        $data = [
            'departure_agency_id' => $departureAgency,
            'arrival_agency_id' => $arrivalAgency,
            'departure_datetime' => $departureDatetime,
            'arrival_datetime' => $arrivalDatetime,
            'total_seats' => $totalSeats,
            'available_seats' => $totalSeats
        ];

        if ($tripModel->update($id, $data)) {
            $_SESSION['flash']['success'] = "Trajet mis à jour avec succès !";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la mise à jour.";
        }

        $this->redirect('/');
    }

    public function delete($id)
    {
        $id = (int)$id; // Sécurité ID
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $this->verifyCsrfToken();

        $tripModel = new Trip();
        $trip = $tripModel->findById($id);

        if (!$trip) {
            $_SESSION['flash']['error'] = "Trajet introuvable.";
            $this->redirect('/');
            return;
        }

        $isAuthor = ($trip['user_id'] == $_SESSION['user']['id']);
        $isAdmin = isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == 1;

        if (!$isAuthor && !$isAdmin) {
            $_SESSION['flash']['error'] = "Vous n'êtes pas autorisé à supprimer ce trajet.";
            $this->redirect('/');
            return;
        }

        if ($tripModel->delete($id)) {
            $_SESSION['flash']['success'] = "Trajet supprimé avec succès !";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la suppression.";
        }

        $this->redirect('/');
    }
}
?>