<?php
session_start();
require 'db_connection.php'; // Add your database connection here

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guess'])) {
        $guess = $_POST['guess'];
        $code = $_SESSION['code'];

        $response = ['status' => 'continue', 'message' => 'Keep trying!'];

        if ($guess === $code) {
            $response['status'] = 'win';
            $response['message'] = 'Congratulations! You broke the code!';
        } else {
            $response['message'] = 'Incorrect guess. Try again!';
        }

        echo json_encode($response);
        exit();
    }

    if (isset($_POST['restart'])) {
        $_SESSION['code'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        echo json_encode(['message' => 'Game restarted!']);
        exit();
    }
}
?>
