<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Siswa.php';

class SiswaController {
    private $db;
    private $siswa;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->siswa = new Siswa($this->db);
    }

    // GET: Ambil Semua Data Siswa
    public function getAllSiswa() {
        $stmt = $this->siswa->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $siswa_arr = array("status" => "success", "data" => array());
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $siswa_item = array(
                    "id" => $id,
                    "nis" => $nis,
                    "nama" => $nama,
                    "kelas" => $kelas
                );
                array_push($siswa_arr["data"], $siswa_item);
            }
            http_response_code(200);
            return json_encode($siswa_arr);
        } else {
            http_response_code(404);
            return json_encode(array("status" => "empty", "message" => "Data siswa tidak ditemukan."));
        }
    }

    // POST: Tambah Siswa Baru
    public function storeSiswa($data) {
        if (!empty($data->nis) && !empty($data->nama) && !empty($data->kelas)) {
            $this->siswa->nis = $data->nis;
            $this->siswa->nama = $data->nama;
            $this->siswa->kelas = $data->kelas;

            if ($this->siswa->create()) {
                http_response_code(201);
                return json_encode(array("status" => "success", "message" => "Siswa berhasil ditambahkan."));
            } else {
                http_response_code(500);
                return json_encode(array("status" => "error", "message" => "Gagal menambahkan siswa."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "Data tidak lengkap."));
        }
    }

    // PUT: Update Data Siswa (Tambahan)
    public function updateSiswa($data) {
        if (!empty($data->id) && !empty($data->nis) && !empty($data->nama) && !empty($data->kelas)) {
            $this->siswa->id = $data->id;
            $this->siswa->nis = $data->nis;
            $this->siswa->nama = $data->nama;
            $this->siswa->kelas = $data->kelas;

            if ($this->siswa->update()) {
                http_response_code(200);
                return json_encode(array("status" => "success", "message" => "Data siswa berhasil diperbarui."));
            } else {
                http_response_code(500);
                return json_encode(array("status" => "error", "message" => "Gagal memperbarui data siswa."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "ID dan data lengkap wajib diisi."));
        }
    }

    // DELETE: Hapus Data Siswa (Tambahan Tugas Jobsheet)
    public function deleteSiswa($data) {
        if (!empty($data->id)) {
            $this->siswa->id = $data->id;

            if ($this->siswa->delete()) {
                http_response_code(200);
                return json_encode(array("status" => "success", "message" => "Siswa berhasil dihapus."));
            } else {
                http_response_code(500);
                return json_encode(array("status" => "error", "message" => "Gagal menghapus siswa."));
            }
        } else {
            http_response_code(400);
            return json_encode(array("status" => "warning", "message" => "ID siswa tidak ditemukan."));
        }
    }
}