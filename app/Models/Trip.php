<?php
namespace App\Models;

use App\Core\Model;

class Trip extends Model
{
    public function findAllAvailable()
    {
        $sql = "SELECT t.*, 
                       da.name as departure_name, 
                       aa.name as arrival_name,
                       u.last_name as author_last_name,
                       u.first_name as author_first_name,
                       u.phone as author_phone,
                       u.email as author_email
                FROM trips t
                INNER JOIN agencies da ON t.departure_agency_id = da.id
                INNER JOIN agencies aa ON t.arrival_agency_id = aa.id
                INNER JOIN users u ON t.user_id = u.id
                WHERE t.available_seats > 0
                  AND t.departure_datetime > NOW()
                ORDER BY t.departure_datetime ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT t.*, 
                       da.name as departure_name, 
                       aa.name as arrival_name,
                       u.last_name as author_last_name,
                       u.first_name as author_first_name,
                       u.phone as author_phone,
                       u.email as author_email
                FROM trips t
                INNER JOIN agencies da ON t.departure_agency_id = da.id
                INNER JOIN agencies aa ON t.arrival_agency_id = aa.id
                INNER JOIN users u ON t.user_id = u.id
                WHERE t.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $sql = "INSERT INTO trips (departure_agency_id, arrival_agency_id, departure_datetime, arrival_datetime, total_seats, available_seats, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['departure_agency_id'],
            $data['arrival_agency_id'],
            $data['departure_datetime'],
            $data['arrival_datetime'],
            $data['total_seats'],
            $data['total_seats'], // available_seats = total_seats
            $data['user_id']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE trips SET 
                    departure_agency_id = ?,
                    arrival_agency_id = ?,
                    departure_datetime = ?,
                    arrival_datetime = ?,
                    total_seats = ?,
                    available_seats = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['departure_agency_id'],
            $data['arrival_agency_id'],
            $data['departure_datetime'],
            $data['arrival_datetime'],
            $data['total_seats'],
            $data['available_seats'],
            $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM trips WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function findAllForAdmin()
    {
        $sql = "SELECT t.*, 
                    da.name as departure_name, 
                    aa.name as arrival_name,
                    u.first_name as author_first_name,
                    u.last_name as author_last_name,
                    u.email as author_email
                FROM trips t
                INNER JOIN agencies da ON t.departure_agency_id = da.id
                INNER JOIN agencies aa ON t.arrival_agency_id = aa.id
                INNER JOIN users u ON t.user_id = u.id
                ORDER BY t.departure_datetime DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

?>