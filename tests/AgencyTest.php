<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Agency;
use App\Config\Database;

class AgencyTest extends TestCase
{
    private $agencyModel;
    private $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo = Database::getInstance();
        $this->agencyModel = new Agency();

        // 1. On nettoie la table (sécurité pour les tests)
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->pdo->exec("DELETE FROM trips");
        $this->pdo->exec("DELETE FROM agencies");
        $this->pdo->exec("ALTER TABLE agencies AUTO_INCREMENT = 1");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // 2. On crée des données de test (Fixtures)
        $this->agencyModel->insert("Paris");
        $this->agencyModel->insert("Lyon");
    }

    public function testFindAllReturnsArray()
    {
        $agencies = $this->agencyModel->findAll();
        $this->assertIsArray($agencies);
        $this->assertCount(2, $agencies, 'Il devrait y avoir 2 agences');
    }

    public function testFindReturnsAgency()
    {
        $agencies = $this->agencyModel->findAll();
        $firstAgency = $agencies[0];
        
        $agency = $this->agencyModel->find($firstAgency['id']);
        
        $this->assertIsArray($agency);
        $this->assertEquals($firstAgency['id'], $agency['id']);
        
        // On compare avec le nom de la première agence trouvée
        $this->assertEquals($firstAgency['name'], $agency['name']); 
    }

    public function testInsertAndDeleteAgency()
    {
        $testName = 'Marseille';
        
        // Insertion
        $insertResult = $this->agencyModel->insert($testName);
        $this->assertTrue($insertResult);
        
        // Vérification
        $stmt = $this->pdo->prepare("SELECT * FROM agencies WHERE name = ?");
        $stmt->execute([$testName]);
        $agency = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertIsArray($agency);
        
        // Suppression
        $deleteResult = $this->agencyModel->delete($agency['id']);
        $this->assertTrue($deleteResult);
        
        // Vérification suppression
        $stmt = $this->pdo->prepare("SELECT * FROM agencies WHERE id = ?");
        $stmt->execute([$agency['id']]);
        $deletedAgency = $stmt->fetch();
        $this->assertFalse($deletedAgency);
    }

    public function testUpdateAgency()
    {
        $agencies = $this->agencyModel->findAll();
        $agency = $agencies[0]; // Paris
        
        $newName = 'Paris 16';
        $updateResult = $this->agencyModel->update($agency['id'], $newName);
        $this->assertTrue($updateResult);
        
        $updatedAgency = $this->agencyModel->find($agency['id']);
        $this->assertEquals($newName, $updatedAgency['name']);
    }
}
?>