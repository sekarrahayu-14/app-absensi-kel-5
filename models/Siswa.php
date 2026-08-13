
<?php
class Siswa {
    private $conn;
    private $table_name = "tb_siswa";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll(): array {
        $query = "SELECT id AS id_siswa, nis, nama_siswa AS nama, kelas, status_kehadiran FROM " . $this->table_name . " ORDER BY kelas ASC, nama_siswa ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllKelas(): array {
        $query = "SELECT DISTINCT kelas FROM " . $this->table_name . " ORDER BY kelas ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return array_column($stmt->fetchAll(), 'kelas');
    }

    public function create(string $nis, string $nama, string $kelas, string $statusKehadiran = 'h'): bool {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . " (nis, nama_siswa, kelas, status_kehadiran) VALUES (:nis, :nama, :kelas, :status_kehadiran)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nis", $nis);
            $stmt->bindParam(":nama", $nama);
            $stmt->bindParam(":kelas", $kelas);
            $stmt->bindParam(":status_kehadiran", $statusKehadiran);
            $stmt->execute();

            $idSiswa = $this->conn->lastInsertId();
            $attendanceQuery = "INSERT INTO tb_kehadiran (id_siswa, tanggal, status_kehadiran, keterangan)
                                VALUES (:id_siswa, :tanggal, :status_kehadiran, :keterangan)";
            $attendanceStmt = $this->conn->prepare($attendanceQuery);
            $tanggal = date('Y-m-d');
            $keterangan = '';
            $attendanceStmt->bindParam(":id_siswa", $idSiswa);
            $attendanceStmt->bindParam(":tanggal", $tanggal);
            $attendanceStmt->bindParam(":status_kehadiran", $statusKehadiran);
            $attendanceStmt->bindParam(":keterangan", $keterangan);
            $attendanceStmt->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Gagal menambahkan siswa dan status kehadiran: " . $exception->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}