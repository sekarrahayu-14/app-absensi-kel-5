<?php
class Presensi {
    private $conn;
    private $table_name = "presensi";

    // Properti sesuai kolom di tabel presensi
    public $id;
    public $siswa_id;
    public $tanggal;
    public $status;
    public $keterangan;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. READ: Ambil Data Presensi (Di-JOIN dengan Tabel Siswa)
    public function read() {
        $query = "SELECT 
                    p.id, 
                    p.siswa_id, 
                    s.nis, 
                    s.nama, 
                    s.kelas, 
                    p.tanggal, 
                    p.status, 
                    p.keterangan 
                  FROM " . $this->table_name . " p
                  JOIN siswa s ON p.siswa_id = s.id
                  ORDER BY p.tanggal DESC, p.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2. CREATE: Tambah Data Presensi Baru
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (siswa_id, tanggal, status, keterangan) 
                  VALUES (:siswa_id, :tanggal, :status, :keterangan)";

        $stmt = $this->conn->prepare($query);

        // Sanitasi Data
        $this->siswa_id = htmlspecialchars(strip_tags($this->siswa_id));
        $this->tanggal = htmlspecialchars(strip_tags($this->tanggal));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->keterangan = htmlspecialchars(strip_tags($this->keterangan));

        // Binding Data
        $stmt->bindParam(":siswa_id", $this->siswa_id);
        $stmt->bindParam(":tanggal", $this->tanggal);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":keterangan", $this->keterangan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // 3. UPDATE: Perbarui Status/Keterangan Presensi
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, keterangan = :keterangan 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitasi Data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->keterangan = htmlspecialchars(strip_tags($this->keterangan));

        // Binding Data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":keterangan", $this->keterangan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // 4. DELETE: Hapus Data Presensi
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitasi Data
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Binding Data
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}