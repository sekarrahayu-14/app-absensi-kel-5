<?php
/**
 * PresensiController.php
 * Menangani logika antara Model Presensi dan tampilan (views/JSON)
 */
require_once __DIR__ . '/../models/Presensi.php';

class PresensiController {
    private $presensi;

    public function __construct() {
        $this->presensi = new Presensi();
    }

    public function listKehadiran($jenis = null, $tanggal = null): array {
        return $this->presensi->getAll($jenis, $tanggal);
    }

    public function summary(string $jenis): array {
        return $this->presensi->getSummaryToday($jenis);
    }

    public function totalSiswa(): int {
        return $this->presensi->countTotalSiswa();
    }

    public function tambah(array $data): bool {
        if (empty($data['jenis']) || empty($data['status'])) {
            throw new InvalidArgumentException('Data jenis dan status wajib diisi');
        }
        if ($data['jenis'] === 'guru' && empty($data['nama_guru'])) {
            throw new InvalidArgumentException('Nama guru wajib diisi');
        }
        if ($data['jenis'] === 'siswa' && empty($data['id_siswa'])) {
            throw new InvalidArgumentException('id_siswa wajib diisi');
        }
        return $this->presensi->create($data);
    }

    public function ubah(int $id, array $data): bool {
        if (empty($data['status'])) {
            throw new InvalidArgumentException('Status wajib diisi');
        }
        return $this->presensi->update($id, $data);
    }

    public function hapus(int $id): bool {
        return $this->presensi->delete($id);
    }
}