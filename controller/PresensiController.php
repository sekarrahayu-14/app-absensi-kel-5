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

    // GET: Ambil Semua Data Riwayat Presensi
    public function getAllPresensi() {
        $stmt = $this->presensi->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $presensi_arr = array("status" => "success", "data" => array());
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $presensi_item = array(
                    "id" => $id,
                    "siswa_id" => $siswa_id,
                    "nis" => $nis,
                    "nama" => $nama,
                    "kelas" => $kelas,
                    "tanggal" => $tanggal,
                    "status" => $status,
                    "keterangan" => $keterangan
                );
                array_push($presensi_arr["data"], $presensi_item);
            }
            http_response_code(200);
            return json_encode($presensi_arr);
        } else {
            http_response_code(404);
            return json_encode(array("status" => "empty", "message" => "Data presensi belum ada."));
        }
    }

    // POST: Catat Presensi Baru
    public function storePresensi($data) {
        if (!empty($data->siswa_id) && !empty($data->tanggal) && !empty($data->status)) {
            $this->presensi->siswa_id = $data->siswa_id;
            $this->presensi->tanggal = $data->tanggal;
            $this->presensi->status = $data->status;
            $this->presensi->keterangan = !empty($data->keterangan) ? $data->keterangan : null;

            if ($this->presensi->create()) {
                http_response_code(201);
                return json_encode(array("status" => "success", "message" => "Presensi berhasil dicatat."));
            } else {
                http_response_code(500);
                return json_encode(array("status" => "error", "message" => "Gagal mencatat presensi."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "Siswa, tanggal, dan status wajib diisi."));
        }
    }

    // PUT: Update Status Presensi
    public function updatePresensi($data) {
        if (!empty($data->id) && !empty($data->status)) {
            $this->presensi->id = $data->id;
            $this->presensi->status = $data->status;
            $this->presensi->keterangan = isset($data->keterangan) ? $data->keterangan : null;

            if ($this->presensi->update()) {
                http_response_code(200);
                return json_encode(array("status" => "success", "message" => "Data presensi berhasil diperbarui."));
            } else {
                http_response_code(500);
                return json_encode(array("status" => "error", "message" => "Gagal memperbarui data presensi."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "ID presensi dan status wajib diisi."));
        }
    }

    // DELETE: Hapus Data Presensi
    public function deletePresensi($data) {
        if (!empty($data->id)) {
            $this->presensi->id = $data->id;

            if ($this->presensi->delete()) {
                http_response_code(200);
                return json_encode(array("status" => "success", "message" => "Data presensi berhasil dihapus."));
            } else {
                http_response_code(500);
                return json_encode(array("status" => "error", "message" => "Gagal menghapus presensi."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "ID presensi tidak ditemukan."));
        }
    }
}