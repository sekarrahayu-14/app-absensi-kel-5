<?php
/**
 * Presensi.php
 * Model untuk tabel tb_kehadiran (dipakai untuk siswa maupun guru, dibedakan kolom `jenis`)
 */
require_once __DIR__ . '/../config/Database.php';

class Presensi {
    private $conn;
    private $table = 'tb_kehadiran';

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Ambil daftar kehadiran, opsional filter jenis ('siswa'/'guru') dan tanggal (Y-m-d)
     */
    public function getAll($jenis = null, $tanggal = null): array {
        $sql = "SELECT k.*, s.nama AS nama_siswa, s.kelas, s.nis
                FROM {$this->table} k
                LEFT JOIN tb_siswa s ON k.id_siswa = s.id_siswa
                WHERE 1=1";
        $params = [];

        if ($jenis) {
            $sql .= " AND k.jenis = :jenis";
            $params[':jenis'] = $jenis;
        }
        if ($tanggal) {
            $sql .= " AND k.tanggal = :tanggal";
            $params[':tanggal'] = $tanggal;
        }
        $sql .= " ORDER BY k.tanggal DESC, k.jam_masuk DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_kehadiran = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO {$this->table}
                (jenis, id_siswa, nama_guru, tanggal, jam_masuk, jam_keluar, status, keterangan)
                VALUES (:jenis, :id_siswa, :nama_guru, :tanggal, :jam_masuk, :jam_keluar, :status, :keterangan)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':jenis'      => $data['jenis'],
            ':id_siswa'   => $data['id_siswa'] ?? null,
            ':nama_guru'  => $data['nama_guru'] ?? null,
            ':tanggal'    => $data['tanggal'] ?? date('Y-m-d'),
            ':jam_masuk'  => $data['jam_masuk'] ?? null,
            ':jam_keluar' => $data['jam_keluar'] ?? null,
            ':status'     => $data['status'],
            ':keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET
                status = :status,
                jam_keluar = :jam_keluar,
                keterangan = :keterangan
                WHERE id_kehadiran = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status'     => $data['status'],
            ':jam_keluar' => $data['jam_keluar'] ?? null,
            ':keterangan' => $data['keterangan'] ?? null,
            ':id'         => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_kehadiran = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Ringkasan status kehadiran hari ini, per jenis (siswa/guru)
     */
    public function getSummaryToday(string $jenis): array {
        $sql = "SELECT status, COUNT(*) AS total
                FROM {$this->table}
                WHERE jenis = :jenis AND tanggal = CURDATE()
                GROUP BY status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':jenis' => $jenis]);
        $rows = $stmt->fetchAll();

        $summary = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
        foreach ($rows as $row) {
            $summary[$row['status']] = (int) $row['total'];
        }
        return $summary;
    }

    public function countTotalSiswa(): int {
        $stmt = $this->conn->query("SELECT COUNT(*) AS total FROM tb_siswa");
        return (int) $stmt->fetch()['total'];
    }
}