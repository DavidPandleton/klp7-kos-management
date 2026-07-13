<?php

namespace App\Models;

use Config\Database;
use PDO;
use App\Helpers\Security;

class Pengaduan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(string $filter = '', array $params = []): array
    {
        $sql = "SELECT a.*, u.username as nama_penyewa, km.nomor_kamar
                FROM pengaduan a
                JOIN users u ON a.penyewa_id = u.id
                LEFT JOIN kamar km ON a.kamar_id = km.id
                WHERE 1=1 $filter
                ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT a.*, u.username as nama_penyewa, u.email, u.no_telepon,
                       km.nomor_kamar
                FROM pengaduan a
                JOIN users u ON a.penyewa_id = u.id
                LEFT JOIN kamar km ON a.kamar_id = km.id
                WHERE a.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && !empty($row['no_telepon']) && strlen($row['no_telepon']) > 30 && str_ends_with($row['no_telepon'], '=')) {
            $decrypted = Security::decrypt($row['no_telepon'], Security::ENCRYPTION_KEY);
            $row['no_telepon'] = $decrypted !== false ? $decrypted : $row['no_telepon'];
        }
        return $row;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pengaduan (penyewa_id, kamar_id, keluhan, foto, status)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['penyewa_id'],
            !empty($data['kamar_id']) ? $data['kamar_id'] : null,
            $data['keluhan'],
            $data['foto'] ?? null,
            'baru'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $respon = null): void
    {
        $sql = "UPDATE pengaduan SET status = ?";
        $params = [$status];

        if ($respon !== null) {
            $sql .= ", respon = ?";
            $params[] = $respon;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function getActive(): array
    {
        $sql = "SELECT a.*, u.username as nama_penyewa, km.nomor_kamar
                FROM pengaduan a
                JOIN users u ON a.penyewa_id = u.id
                LEFT JOIN kamar km ON a.kamar_id = km.id
                WHERE a.status IN ('baru', 'diproses')
                ORDER BY a.created_at ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function countActive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM pengaduan WHERE status IN ('baru', 'diproses')")->fetchColumn();
    }
}
