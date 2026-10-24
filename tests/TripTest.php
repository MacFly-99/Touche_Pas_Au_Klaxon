<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Trip;
use App\Models\Agency;
use App\Models\User;
use App\Config\Database;

class TripTest extends TestCase
{
    private $tripModel;
    private $pdo;
    private $userId;
    private $agency1Id;
    private $agency2Id;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = Database::getInstance();
        $this->tripModel = new Trip();

        // 1. Nettoyage complet
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->pdo->exec("DELETE FROM trips");
        $this->pdo->exec("DELETE FROM users");
        $this->pdo->exec("DELETE FROM agencies");
        $this->pdo->exec("ALTER TABLE agencies AUTO_INCREMENT = 1");
        $this->pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
        $this->pdo->exec("ALTER TABLE trips AUTO_INCREMENT = 1");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // 2. Création des Fixtures (Agences et Utilisateur)
        $agencyModel = new Agency();
        $agencyModel->insert("Paris");
        $agencyModel->insert("Lyon");
        $this->agency1Id = 1;
        $this->agency2Id = 2;

        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Martin', 'Alexandre', '0612345678', 'alexandre@test.fr', $hash, 0]);
        $this->userId = $this->pdo->lastInsertId();

        // 3. Création d'un trajet futur (pour les tests de lecture)
        $data = [
            'departure_agency_id' => $this->agency1Id,
            'arrival_agency_id' => $this->agency2Id,
            'departure_datetime' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime('+7 days + 2 hours')),
            'total_seats' => 4,
            'available_seats' => 4,
            'user_id' => $this->userId
        ];
        $this->tripModel->insert($data);
    }

    public function testFindAllAvailableReturnsArray()
    {
        $trips = $this->tripModel->findAllAvailable();
        $this->assertIsArray($trips);
        $this->assertNotEmpty($trips, 'Il devrait y avoir au moins un trajet futur');
    }

    public function testFindAllAvailableOnlyReturnsFutureTrips()
    {
        $trips = $this->tripModel->findAllAvailable();
        $now = new \DateTime();
        
        foreach ($trips as $trip) {
            $departure = new \DateTime($trip['departure_datetime']);
            $this->assertGreaterThan($now, $departure, 'Le trajet doit être dans le futur');
            $this->assertGreaterThan(0, $trip['available_seats'], 'Le trajet doit avoir des places');
        }
    }

    public function testFindByIdReturnsTrip()
    {
        $trip = $this->tripModel->findById(1); // Le trajet créé dans le setUp
        
        $this->assertIsArray($trip);
        $this->assertEquals(1, $trip['id']);
        $this->assertArrayHasKey('departure_name', $trip);
        $this->assertEquals('Paris', $trip['departure_name']);
        $this->assertArrayHasKey('author_first_name', $trip);
    }

    public function testInsertAndDeleteTrip()
    {
        $newData = [
            'departure_agency_id' => $this->agency2Id,
            'arrival_agency_id' => $this->agency1Id,
            'departure_datetime' => date('Y-m-d H:i:s', strtotime('+10 days')),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime('+10 days + 1 hour')),
            'total_seats' => 2,
            'available_seats' => 2,
            'user_id' => $this->userId
        ];

        // Insertion
        $insertResult = $this->tripModel->insert($newData);
        $this->assertTrue($insertResult);

        $lastId = $this->pdo->lastInsertId();
        
        // Vérification
        $trip = $this->tripModel->findById($lastId);
        $this->assertIsArray($trip);
        $this->assertEquals('Lyon', $trip['departure_name']);

        // Suppression
        $deleteResult = $this->tripModel->delete($lastId);
        $this->assertTrue($deleteResult);

        // Vérification suppression
        $deletedTrip = $this->tripModel->findById($lastId);
        $this->assertFalse($deletedTrip);
    }
}
?>