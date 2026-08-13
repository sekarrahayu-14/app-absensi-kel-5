<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/controller/KehadiranController.php';

$controller = new KehadiranController();
$request_method = $_SERVER["REQUEST_METHOD"];
$tanggal = $_POST['tanggal'] ?? date ('Y-m-d');

switch ($request_method) {
    case 'GET':
        if (($_GET['action'] ?? '') === 'summary') {
            echo $controller->getSummary($tanggal);
        }else{
            echo $controller->getKehadiran($tanggal);
        }
        break;

    case 'POST':
        $data  = json_decode(file_get_contents("php://input"));
        echo $controller->storeKehadiran(data);
        break;

    default:
       http_response_code(405);
       echo json_encode(array("message" => "Metode HTTP tidak diizinkan"));
        break;
}
?>