<?php
<<<<<<< HEAD
header("Acces-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Acces-Control-Allow-Methods: GET, POST, DELATE");
header("Acces-Control-Allow-Headers: Content-Type, Acces-Control-Allow-Headers, Authorization, X-Requested-With");
=======
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once __DIR__ . '/..config/Database.php';
    require_once __DIR__ . '/../controller/SiswaController.php';

    $database = new Database();
    $connection = $database->getConnection();

    if ($connection === null) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database telah dapat dihubungi. Periksa MySQL, nama database, username, dan password."
        ]);
        exit;
    }

    $controller = new SiswaController($connection);
    $request_method = $_SERVER["REQUEST_METHOD"];

    switch ($request_method) {
        case 'GET':
            if (($_GET['action'] ?? '') === 'kelas') {
                $controller->getKelas();
            }else{
                $controller->getSiswa();
            }
            break;
        

        case 'POST':
                $controller->createSiswa();
            break;

        case 'DELETE':
                $id = $_GET['id'] ?? null;
                $controller->deleteSiswa((int) $id);
            break;

        default:
           http_response_code(405);
           echo json_encode(array("message" => "Metode HTTP tidak diizinkan"));
            break;
    }
?>
>>>>>>> fd12c658b63028e744c2b818b83a04bef74457e5
