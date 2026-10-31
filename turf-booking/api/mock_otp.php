<?php
header('Content-Type: application/json');
// Simulates sending OTP
echo json_encode(["status" => "success", "message" => "OTP 1234 sent to phone."]);
?>
