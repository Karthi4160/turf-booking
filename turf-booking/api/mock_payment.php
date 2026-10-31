<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"));
// Simulates Payment Gateway processing
if ($data && $data->amount > 0) {
    echo json_encode(["status" => "success", "txn_id" => "TXN" . rand(10000, 99999)]);
} else {
    echo json_encode(["status" => "failed", "message" => "Invalid Amount"]);
}
?>
