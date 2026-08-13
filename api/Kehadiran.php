<?php
header("Acces-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Acces-Control-Allow-Methods: GET, POST");
header("Acces-Control-Allow-Headers: Content-Type, Acces-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/Databse.php';
require_once __DIR__ . '/../controllers/KehadiranController.php';

$controller = new KehadiranController();
$request_method = $_SERVER["REQUEST_METHOD"];
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

switch ($request_method) {
    case 'GET':
        if (($_GET['action'] ?? '') === 'summary'){
            echo $controller->getSummary($tanggal);
        } else {
            echo $controller->getKehadiran($tanggal);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        echo $controller->strokeKehadiran($data);
        break;

    default:
        httpp_response_code(405);
        echo json_encode(array("message" => "Methods HTTP tidak diizinkan"));
        break;
}
?>