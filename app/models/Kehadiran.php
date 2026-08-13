
<?php
class Kehadiran {
    private $conn;
    private $table_name = "tb_kehadiran";

    public $id;
    public $id_siswa;
    public $tanggal;
    public $jam_masuk;
    public $status;
    public $keterangan;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Gabungkan data siswa + kehadiran pada tanggal tertentu
    public function readByTanggal($tanggal) {
        $query = "SELECT k.id_kehadiran, k.tanggal, k.status_kehadiran, k.keterangan,
                         s.id AS id_siswa, s.nis, s.nama_siswa AS nama, s.kelas
                  FROM tb_siswa s
                  LEFT JOIN " . $this->table_name . " k ON k.id_siswa = s.id AND k.tanggal = :tanggal
                  ORDER BY s.kelas ASC, s.nama_siswa ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tanggal", $tanggal);
        $stmt->execute();
        return $stmt;
    }

    // Ringkasan jumlah per status untuk tanggal tertentu
    public function summary($tanggal) {
        $query = "SELECT status_kehadiran, COUNT(*) as jumlah FROM " . $this->table_name . "
                  WHERE tanggal = :tanggal GROUP BY status_kehadiran";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tanggal", $tanggal);
        $stmt->execute();

        $hasil = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $status = ['h' => 'Hadir', 'a' => 'Alpha', 's' => 'Sakit', 'i' => 'Izin'];
            if (isset($status[$row['status_kehadiran']])) {
                $hasil[$status[$row['status_kehadiran']]] = (int) $row['jumlah'];
            }
        }
        return $hasil;
    }

    // Simpan / update presensi (insert baru, atau update jika sudah absen hari itu)
    public function checkIn() {
        $this->id_siswa = htmlspecialchars(strip_tags($this->id_siswa));
        $status = ['Hadir' => 'h', 'Alpha' => 'a', 'Sakit' => 's', 'Izin' => 'i'];
        $this->status = $status[$this->status] ?? $this->status;
        $this->keterangan = $this->keterangan ? htmlspecialchars(strip_tags($this->keterangan)) : null;
        $this->tanggal = date('Y-m-d');

        $query = "INSERT INTO " . $this->table_name . " (id_siswa, tanggal, status_kehadiran, keterangan)
              VALUES (:id_siswa, :tanggal, :status_kehadiran, :keterangan)
              ON DUPLICATE KEY UPDATE status_kehadiran = VALUES(status_kehadiran), keterangan = VALUES(keterangan)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_siswa", $this->id_siswa);
        $stmt->bindParam(":tanggal", $this->tanggal);
        $stmt->bindParam(":status_kehadiran", $this->status);
        $stmt->bindParam(":keterangan", $this->keterangan);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}