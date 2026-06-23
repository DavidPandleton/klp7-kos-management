<?php

namespace App\Models;

use Config\Database;
use PDO;

class Kontrak
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(string $filter = '', array $params = []): array
    {
        $sql = "SELECT k.*, u.username as nama_penyewa, km.nomor_kamar
                FROM kontrak k
                JOIN users u ON k.penyewa_id = u.id
                JOIN kamar km ON k.kamar_id = km.id
                WHERE 1=1 $filter
                ORDER BY k.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT k.*, u.username as nama_penyewa, u.email, u.no_telepon,
                       km.nomor_kamar, km.harga, km.tipe
                FROM kontrak k
                JOIN users u ON k.penyewa_id = u.id
                JOIN kamar km ON k.kamar_id = km.id
                WHERE k.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO kontrak (penyewa_id, kamar_id, tgl_mulai, tgl_akhir, status) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['penyewa_id'],
                $data['kamar_id'],
                $data['tgl_mulai'],
                $data['tgl_akhir'],
                $data['status'] ?? 'aktif'
            ]);
            $kontrakId = (int) $this->db->lastInsertId();
            $this->db->prepare("UPDATE kamar SET status = 'terisi' WHERE id = ?")->execute([$data['kamar_id']]);
            $this->db->commit();
            return $kontrakId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): void
    {
        $kontrak = $this->find($id);
        $this->db->beginTransaction();
        try {
            $this->db->prepare("UPDATE kontrak SET status = ? WHERE id = ?")->execute([$status, $id]);
            if ($status === 'selesai' || $status === 'dibatalkan') {
                $this->db->prepare("UPDATE kamar SET status = 'tersedia' WHERE id = ?")->execute([$kontrak['kamar_id']]);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getActiveByPenyewa(int $penyewaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT k.*, km.nomor_kamar, km.harga FROM kontrak k
             JOIN kamar km ON k.kamar_id = km.id
             WHERE k.penyewa_id = ? AND k.status = 'aktif'"
        );
        $stmt->execute([$penyewaId]);
        return $stmt->fetchAll();
    }

    public function countActive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM kontrak WHERE status = 'aktif'")->fetchColumn();
    }
}
