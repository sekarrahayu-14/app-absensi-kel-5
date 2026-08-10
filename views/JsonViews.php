<?php
    class JsonView  
    {public static function render(int $statusCode, array $data): void  {
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        http_response_code($statusCode);echo json_encode($data);
        exit; }
        }
        
        ?>