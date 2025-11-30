<?php
// FILE: includes/response.php

class Response {
    
    // Kirim Sukses (200 OK)
    public static function json($data, $message = 'Success') {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // Kirim Error (400/500)
    public static function error($message, $code = 400) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'data' => null
        ]);
        exit;
    }
}
?>