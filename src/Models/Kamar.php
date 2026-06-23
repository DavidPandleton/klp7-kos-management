<?php

namespace App\Models;

use Config\Database;
use PDO;

class Kamar
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(string $filter = '', array $params = []): array
    {
        $sql = "SELECT * FROM kamar WHERE 1=1 $filter ORDER BY nomor_kamar";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM kamar WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO kamar (nomor_kamar, tipe, harga, kapasitas, fasilitas, status, foto)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nomor_kamar'],
            $data['tipe'],
            $data['harga'],
            $data['kapasitas'] ?? 1,
            $data['fasilitas'] ?? '',
            $data['status'] ?? 'tersedia',
            $data['foto'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE kamar SET nomor_kamar=?, tipe=?, harga=?, kapasitas=?, fasilitas=?, status=?";
        $params = [$data['nomor_kamar'], $data['tipe'], $data['harga'], $data['kapasitas'] ?? 1, $data['fasilitas'] ?? '', $data['status'] ?? 'tersedia'];

        if (!empty($data['foto'])) {
            $sql .= ", foto=?";
            $params[] = $data['foto'];
        }

        $sql .= " WHERE id=?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM kamar WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM kamar WHERE status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function totalKamar(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM kamar")->fetchColumn();
    }
}
