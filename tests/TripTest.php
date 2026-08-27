<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Trip;
use App\Config\Database;

class TripTest extends TestCase
{
    private $tripModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tripModel = new Trip();
        
        // Nettoyer la table avant chaque test (optionnel)
        // $this->tripModel->db->exec("DELETE FROM trips");
    }

    public function testFindAllAvailableReturnsArray()
    {
        $trips = $this->tripModel->findAllAvailable();
        $this->assertIsArray($trips, 'findAllAvailable should return an array');
    }

    public function testFindAllAvailableOnlyReturnsFutureTrips()
    {
        $trips = $this->tripModel->findAllAvailable();
        
        if (!empty($trips)) {
            $now = new \DateTime();
            foreach ($trips as $trip) {
                $departure = new \DateTime($trip['departure_datetime']);
                $this->assertGreaterThan(
                    $now, 
                    $departure, 
                    'Trip departure should be in the future'
                );
                $this->assertGreaterThan(
                    0, 
                    $trip['available_seats'], 
                    'Trip should have available seats'
                );
            }
        } else {
            $this->markTestSkipped('No trips available for testing');
        }
    }

    public function testFindByIdReturnsTrip()
    {
        // Récupérer un trajet existant (on utilise le premier)
        $trips = $this->tripModel->findAllAvailable();
        
        if (empty($trips)) {
            $this->markTestSkipped('No trips available for testing');
            return;
        }
        
        $firstTrip = $trips[0];
        $trip = $this->tripModel->findById($firstTrip['id']);
        
        $this->assertIsArray($trip, 'findById should return an array');
        $this->assertEquals($firstTrip['id'], $trip['id'], 'Trip ID should match');
        $this->assertArrayHasKey('departure_name', $trip, 'Trip should have departure_name');
        $this->assertArrayHasKey('arrival_name', $trip, 'Trip should have arrival_name');
        $this->assertArrayHasKey('author_first_name', $trip, 'Trip should have author_first_name');
        $this->assertArrayHasKey('author_last_name', $trip, 'Trip should have author_last_name');
        $this->assertArrayHasKey('author_phone', $trip, 'Trip should have author_phone');
        $this->assertArrayHasKey('author_email', $trip, 'Trip should have author_email');
    }

    public function testInsertAndDeleteTrip()
    {
        // Récupérer une agence existante
        $stmt = $this->tripModel->getDb()->query("SELECT id FROM agencies LIMIT 1");
        $agency = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$agency) {
            $this->markTestSkipped('No agencies available for testing');
            return;
        }

        // Récupérer un utilisateur existant
        $stmt = $this->tripModel->getDb()->query("SELECT id FROM users LIMIT 1");
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$user) {
            $this->markTestSkipped('No users available for testing');
            return;
        }

        // Données de test
        $data = [
            'departure_agency_id' => $agency['id'],
            'arrival_agency_id' => $agency['id'] + 1, // Supposons qu'il y a au moins 2 agences
            'departure_datetime' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime('+7 days + 2 hours')),
            'total_seats' => 5,
            'user_id' => $user['id']
        ];

        // Vérifier que l'agence d'arrivée existe
        $stmt = $this->tripModel->getDb()->prepare("SELECT id FROM agencies WHERE id = ?");
        $stmt->execute([$data['arrival_agency_id']]);
        if (!$stmt->fetch()) {
            $data['arrival_agency_id'] = $agency['id'];
        }

        // Insertion
        $insertResult = $this->tripModel->insert($data);
        $this->assertTrue($insertResult, 'Insert should return true');

        // Récupérer le dernier ID inséré
        $lastId = $this->tripModel->getDb()->lastInsertId();
        $this->assertNotEmpty($lastId, 'Last insert ID should not be empty');

        // Vérifier que le trajet existe bien
        $trip = $this->tripModel->findById($lastId);
        $this->assertIsArray($trip, 'Trip should exist after insert');
        $this->assertEquals($data['departure_agency_id'], $trip['departure_agency_id']);

        // Suppression
        $deleteResult = $this->tripModel->delete($lastId);
        $this->assertTrue($deleteResult, 'Delete should return true');

        // Vérifier que le trajet n'existe plus
        $trip = $this->tripModel->findById($lastId);
        $this->assertFalse($trip, 'Trip should not exist after delete');
    }
}

?>