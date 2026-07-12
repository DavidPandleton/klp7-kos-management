<?php

namespace App\Models;

use Config\Database;
use PDO;

class Pembayaran
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(string $filter = '', array $params = []): array
    {
        $sql = "SELECT p.*, u.username as nama_penyewa, km.nomor_kamar
                FROM pembayaran p
                JOIN kontrak k ON p.kontrak_id = k.id
                JOIN users u ON k.penyewa_id = u.id
                JOIN kamar km ON k.kamar_id = km.id
                WHERE 1=1 $filter
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT p.*, u.username as nama_penyewa, u.email,
                       km.nomor_kamar, km.harga
                FROM pembayaran p
                JOIN kontrak k ON p.kontrak_id = k.id
                JOIN users u ON k.penyewa_id = u.id
                JOIN kamar km ON k.kamar_id = km.id
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO pembayaran (kontrak_id, bulan, tahun, jumlah, bukti, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['kontrak_id'],
            $data['bulan'],
            $data['tahun'],
            $data['jumlah'],
            $data['bukti'] ?? null,
            $data['status'] ?? 'belum_bayar'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function konfirmasi(int $id): void
    {
        $bayar = $this->find($id);
        if (!$bayar) return;

        $tglJatuhTempo = sprintf('%04d-%02d-10', $bayar['tahun'], $bayar['bulan']);
        $hariIni = new \DateTime();
        $jatuhTempo = new \DateTime($tglJatuhTempo);
        $denda = 0;

        if ($hariIni > $jatuhTempo) {
            $selisih = (int) $hariIni->diff($jatuhTempo)->days;
            $denda = $selisih * 5000;
        }

        $stmt = $this->db->prepare("UPDATE pembayaran SET status = 'lunas', tgl_bayar = CURDATE(), denda = ? WHERE id = ?");
        $stmt->execute([$denda, $id]);

        $stmt = $this->db->prepare("UPDATE pembayaran SET status = 'lunas' WHERE kontrak_id = ? AND bulan = ? AND tahun = ? AND status = 'belum_bayar'");
        $stmt->execute([$bayar['kontrak_id'], $bayar['bulan'], $bayar['tahun']]);
    }

    public function tolak(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE pembayaran SET status = 'belum_bayar', bukti = NULL WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getByKontrak(int $kontrakId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM pembayaran WHERE kontrak_id = ? ORDER BY tahun DESC, bulan DESC");
        $stmt->execute([$kontrakId]);
        return $stmt->fetchAll();
    }

    public function getUnpaidByKontrak(int $kontrakId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM pembayaran WHERE kontrak_id = ? AND status = 'belum_bayar' ORDER BY tahun, bulan"
        );
        $stmt->execute([$kontrakId]);
        return $stmt->fetchAll();
    }

    public function getUnconfirmed(): array
    {
        $sql = "SELECT p.*, u.username as nama_penyewa, km.nomor_kamar
                FROM pembayaran p
                JOIN kontrak k ON p.kontrak_id = k.id
                JOIN users u ON k.penyewa_id = u.id
                JOIN kamar km ON k.kamar_id = km.id
                WHERE p.status = 'menunggu'
                ORDER BY p.created_at ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getMonthlyRevenue(int $bulan, int $tahun): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(jumlah + denda), 0) FROM pembayaran
             WHERE MONTH(tgl_bayar) = ? AND YEAR(tgl_bayar) = ? AND status = 'lunas'"
        );
        $stmt->execute([$bulan, $tahun]);
        return (float) $stmt->fetchColumn();
    }
}
