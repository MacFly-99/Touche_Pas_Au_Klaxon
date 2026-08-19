<?php
namespace App\Models;

use App\Core\Model;

class Agency extends Model
{
    public function findAll()
    {
        $sql = "SELECT * FROM agencies ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM agencies WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function insert($name)
    {
        $sql = "INSERT INTO agencies (name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name]);
    }

    public function update($id, $name)
    {
        $sql = "UPDATE agencies SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM agencies WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}

?>