<?php
/**
 * JsonViews.php
 * Endpoint API JSON untuk data kehadiran siswa & guru.
 *
 * Contoh pemanggilan:
 *   GET  JsonViews.php?action=list&jenis=guru
 *   GET  JsonViews.php?action=list&jenis=siswa&tanggal=2026-08-10
 *   GET  JsonViews.php?action=summary&jenis=guru
 *   POST JsonViews.php?action=tambah      (body JSON)
 *   POST JsonViews.php?action=ubah        (body JSON, sertakan id_kehadiran)
 *   GET  JsonViews.php?action=hapus&id=5
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../controller/PresensiController.php';

$controller = new PresensiController();
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {

        case 'list':
            $jenis   = $_GET['jenis'] ?? null;
            $tanggal = $_GET['tanggal'] ?? null;
            $data = $controller->listKehadiran($jenis, $tanggal);
            echo json_encode(['status' => 'success', 'data' => $data]);
            break;

        case 'summary':
            $jenis = $_GET['jenis'] ?? 'siswa';
            $data = $controller->summary($jenis);
            $totalSiswa = $jenis === 'siswa' ? $controller->totalSiswa() : null;
            echo json_encode([
                'status' => 'success',
                'data'   => $data,
                'total_siswa' => $totalSiswa
            ]);
            break;

        case 'tambah':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $ok = $controller->tambah($input);
            echo json_encode(['status' => $ok ? 'success' : 'error']);
            break;

        case 'ubah':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = (int) ($input['id_kehadiran'] ?? 0);
            if (!$id) {
                throw new InvalidArgumentException('id_kehadiran wajib diisi');
            }
            $ok = $controller->ubah($id, $input);
            echo json_encode(['status' => $ok ? 'success' : 'error']);
            break;

        case 'hapus':
            $id = (int) ($_GET['id'] ?? 0);
            if (!$id) {
                throw new InvalidArgumentException('id wajib diisi');
            }
            $ok = $controller->hapus($id);
            echo json_encode(['status' => $ok ? 'success' : 'error']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenali']);
    }
} catch (InvalidArgumentException $e) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}