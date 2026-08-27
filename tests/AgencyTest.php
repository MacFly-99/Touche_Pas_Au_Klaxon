<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Agency;

class AgencyTest extends TestCase
{
    private $agencyModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agencyModel = new Agency();
    }

    public function testFindAllReturnsArray()
    {
        $agencies = $this->agencyModel->findAll();
        $this->assertIsArray($agencies, 'findAll should return an array');
        $this->assertNotEmpty($agencies, 'findAll should return at least one agency');
    }

    public function testFindReturnsAgency()
    {
        // Récupérer la première agence
        $agencies = $this->agencyModel->findAll();
        if (empty($agencies)) {
            $this->markTestSkipped('No agencies available for testing');
            return;
        }
        
        $firstAgency = $agencies[0];
        $agency = $this->agencyModel->find($firstAgency['id']);
        
        $this->assertIsArray($agency, 'find should return an array');
        $this->assertEquals($firstAgency['id'], $agency['id']);
        $this->assertEquals($firstAgency['name'], $agency['name']);
    }

    public function testInsertAndDeleteAgency()
    {
        $testName = 'Test Agency ' . time();
        
        // Insertion
        $insertResult = $this->agencyModel->insert($testName);
        $this->assertTrue($insertResult, 'Insert should return true');
        
        // Récupérer l'agence créée
        $stmt = $this->agencyModel->getDb()->prepare("SELECT * FROM agencies WHERE name = ?");
        $stmt->execute([$testName]);
        $agency = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $this->assertIsArray($agency, 'Agency should exist after insert');
        $this->assertEquals($testName, $agency['name']);
        
        // Suppression
        $deleteResult = $this->agencyModel->delete($agency['id']);
        $this->assertTrue($deleteResult, 'Delete should return true');
        
        // Vérifier que l'agence n'existe plus
        $stmt = $this->agencyModel->getDb()->prepare("SELECT * FROM agencies WHERE name = ?");
        $stmt->execute([$agency['id']]);
        $deletedAgency = $stmt->fetch();
        $this->assertFalse($deletedAgency, 'Agency should not exist after delete');
    }

    public function testUpdateAgency()
    {
        // Créer une agence de test
        $testName = 'Temp Agency ' . time();
        $this->agencyModel->insert($testName);
        
        $stmt = $this->agencyModel->getDb()->prepare("SELECT * FROM agencies WHERE name = ?");
        $stmt->execute([$testName]);
        $agency = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$agency) {
            $this->markTestSkipped('Could not create test agency');
            return;
        }
        
        // Mise à jour
        $newName = 'Updated Agency ' . time();
        $updateResult = $this->agencyModel->update($agency['id'], $newName);
        $this->assertTrue($updateResult, 'Update should return true');
        
        // Vérifier la mise à jour
        $updatedAgency = $this->agencyModel->find($agency['id']);
        $this->assertEquals($newName, $updatedAgency['name']);
        
        // Nettoyer
        $this->agencyModel->delete($agency['id']);
    }
}

?>