<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Presensi.php';

class PresensiController {
    private $db;
    private $presensi;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->presensi = new Presensi($this->db);
    }

    public function getJsonData() {
        header('Content-Type: application/json');
        $stmt = $this->presensi->readAllWithSiswa();
        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = [
                'nis' => $row['nis'],
                'nama_siswa' => $row['nama_siswa'],
                'kelas' => $row['kelas'],
                'jurusan' => $row['jurusan'],
                'tanggal' => $row['tanggal'] ?? date('Y-m-d'),
                'status_kehadiran' => $row['status_kehadiran'] ?? 'Hadir',
                'keterangan' => $row['keterangan'] ?? '-'
            ];
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function submitAbsensi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nis_list = $_POST['nis'] ?? [];
            $status_list = $_POST['status_kehadiran'] ?? [];
            $keterangan_list = $_POST['keterangan'] ?? [];
            $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

            foreach ($nis_list as $index => $nis) {
                $status = $status_list[$index] ?? 'Hadir';
                $keterangan = $keterangan_list[$index] ?? '-';
                $this->presensi->simpanAbsensi($nis, $tanggal, $status, $keterangan);
            }
            header("Location: ../views/KehadiranSiswa.php?status=success");
            exit;
        }
    }
}