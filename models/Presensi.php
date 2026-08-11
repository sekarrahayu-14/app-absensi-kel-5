<?php
class Presensi {
    private $conn;
    private $table_kehadiran = "tb_kehadiran";
    private $table_siswa = "tb_siswa";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mengambil semua siswa beserta data kehadirannya
    public function readAllWithSiswa() {
        $query = "SELECT s.nis, s.nama_siswa, s.kelas, s.jurusan, k.tanggal, k.status_kehadiran, k.keterangan 
                  FROM " . $this->table_siswa . " s 
                  LEFT JOIN " . $this->table_kehadiran . " k ON s.nis = k.nis
                  ORDER BY s.nama_siswa ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Menyimpan atau memperbarui data absensi
    public function simpanAbsensi($nis, $tanggal, $status, $keterangan) {
        $checkQuery = "SELECT id FROM " . $this->table_kehadiran . " WHERE nis = :nis AND tanggal = :tanggal";
        $stmtCheck = $this->conn->prepare($checkQuery);
        $stmtCheck->bindParam(":nis", $nis);
        $stmtCheck->bindParam(":tanggal", $tanggal);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            $query = "UPDATE " . $this->table_kehadiran . " 
                      SET status_kehadiran = :status, keterangan = :keterangan 
                      WHERE nis = :nis AND tanggal = :tanggal";
        } else {
            $query = "INSERT INTO " . $this->table_kehadiran . " (nis, tanggal, status_kehadiran, keterangan) 
                      VALUES (:nis, :tanggal, :status, :keterangan)";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nis", $nis);
        $stmt->bindParam(":tanggal", $tanggal);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":keterangan", $keterangan);

        return $stmt->execute();
    }
}