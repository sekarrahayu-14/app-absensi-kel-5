
<?php
require_once __DIR__ . '/../models/Siswa.php';
require_once __DIR__ . '/../views/JsonViews.php';

class SiswaController
{
    private $siswaModel;

    public function __construct(?PDO $db)
    {
        $this->siswaModel = new Siswa($db);
    }
    // Handle GET /index.php?resource=siswa
    public function getSiswa(): void
    {
        $data = $this->siswaModel->getAll();
        JsonView::render(200, [
            "status" => "success",
            "message" => "Data siswa berhasil diambil",
            "data" => $data
        ]);
    }

    // Handle GET /index.php?resource=siswa&action=kelas
    public function getKelas(): void
    {
        $data = $this->siswaModel->getAllKelas();
        JsonView::render(200, [
            "status" => "success",
            "message" => "Daftar kelas berhasil diambil",
            "data" => $data
        ]);
    }

    // Handle POST /index.php?resource=siswa
    public function createSiswa(): void
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!empty($input['nis']) && !empty($input['nama']) && !empty($input['kelas'])) {
            $statusKehadiran = $input['status_kehadiran'] ?? 'h';
            $statusValid = ['h', 'a', 's', 'i'];
            if (!in_array($statusKehadiran, $statusValid, true)) {
                JsonView::render(400, [
                    "status" => "error",
                    "message" => "Status kehadiran tidak valid."
                ]);
                return;
            }

            $success = $this->siswaModel->create($input['nis'], $input['nama'], $input['kelas'], $statusKehadiran);

            if ($success) {
                JsonView::render(200, [
                    "status" => "success",
                    "message" => "Siswa berhasil ditambahkan"
                ]);
            } else {
                JsonView::render(500, [
                    "status" => "error",
                    "message" => "Gagal menambahkan siswa"
                ]);
            }
        } else {
            JsonView::render(400, [
                "status" => "error",
                "message" => "Data nis, nama, dan kelas wajib diisi!"
            ]);
        }
    }

    // Handle DELETE /index.php?resource=siswa&id=1
    public function deleteSiswa(int $id): void
    {
        $success = $this->siswaModel->delete($id);
        if ($success) {
            JsonView::render(200, [
                "status" => "success",
                "message" => "Siswa berhasil dihapus"
            ]);
        } else {
            JsonView::render(500, [
                "status" => "error",
                "message" => "Gagal menghapus siswa"
            ]);
        }
    }
}