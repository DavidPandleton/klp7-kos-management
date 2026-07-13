<?php

namespace App\Models;

use Config\Database;
use PDO;
use App\Helpers\Security;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    private function encryptTelepon(?string $telp): ?string
    {
        return $telp ? Security::encrypt($telp, Security::ENCRYPTION_KEY) : null;
    }

    private function isEncrypted(string $value): bool
    {
        return strlen($value) > 30 && str_ends_with($value, '=');
    }

    private function decryptTelepon(?string $telp): ?string
    {
        if (!$telp || !$this->isEncrypted($telp)) return $telp;
        $decrypted = Security::decrypt($telp, Security::ENCRYPTION_KEY);
        return $decrypted !== false ? $decrypted : $telp;
    }

    private function decryptRow(array|false $row): array|false
    {
        if ($row === false) return false;
        $row['no_telepon'] = $this->decryptTelepon($row['no_telepon'] ?? null);
        return $row;
    }

    private function decryptRows(array $rows): array
    {
        return array_map(fn($r) => $this->decryptRow($r), $rows);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $this->decryptRows($stmt->fetchAll());
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $this->decryptRow($stmt->fetch());
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $this->decryptRow($stmt->fetch());
    }

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $this->decryptRow($stmt->fetch());
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, role, no_telepon, alamat)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'] ?? 'penyewa',
            $this->encryptTelepon($data['no_telepon'] ?? null),
            $data['alamat'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE users SET username=?, email=?, role=?, no_telepon=?, alamat=?";
        $params = [$data['username'], $data['email'], $data['role'] ?? 'penyewa', $this->encryptTelepon($data['no_telepon'] ?? null), $data['alamat'] ?? null];

        if (!empty($data['password'])) {
            $sql .= ", password=?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id=?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getByRole(string $role): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? ORDER BY username");
        $stmt->execute([$role]);
        return $this->decryptRows($stmt->fetchAll());
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }
}
