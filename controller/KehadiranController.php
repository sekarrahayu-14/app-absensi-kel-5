


<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Kehadiran.php';

class KehadiranController {
    private $db;
    private $kehadiran;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->kehadiran = new Kehadiran($this->db);
    }

    public function getKehadiran($tanggal) {
        $tanggal = $tanggal ?: date('Y-m-d');
        $stmt = $this->kehadiran->readByTanggal($tanggal);
        $num = $stmt->rowCount();

        $kehadiran_arr = array("status" => "success", "data" => array());
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $kehadiran_arr["data"][] = array(
                "id_siswa" => $id_siswa,
                "nis" => $nis,
                "nama" => $nama,
                "kelas" => $kelas,
                "status" => ["h" => "Hadir", "a" => "Alpha", "s" => "Sakit", "i" => "Izin"][$status_kehadiran] ?? "Belum Absen"
            );
        }
        http_response_code(200);
        return json_encode($kehadiran_arr);
    }

    public function getSummary($tanggal) {
        $tanggal = $tanggal ?: date('Y-m-d');
        $summary = $this->kehadiran->summary($tanggal);
        http_response_code(200);
        return json_encode(array("status" => "success", "data" => $summary));
    }

    public function storeKehadiran($data) {
        $validStatus = ['Hadir', 'Sakit', 'Izin', 'Alpha'];
        if (!empty($data->id_siswa) && !empty($data->status) && in_array($data->status, $validStatus, true)) {
            $this->kehadiran->id_siswa = $data->id_siswa;
            $this->kehadiran->status = $data->status;
            $this->kehadiran->keterangan = $data->keterangan ?? null;

            if ($this->kehadiran->checkIn()) {
                http_response_code(201);
                return json_encode(array("status" => "success", "message" => "Presensi berhasil disimpan."));
            } else {
                http_response_code(503);
                return json_encode(array("status" => "error", "message" => "Gagal menyimpan presensi."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "Data id_siswa dan status wajib diisi."));
        }
    }
}