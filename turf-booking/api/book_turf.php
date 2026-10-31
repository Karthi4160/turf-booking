<?php
include '../db.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"));

// Server-side availability check (booked or blocked)
$stmt = $conn->prepare("SELECT id FROM bookings WHERE booking_date = ? AND slot_time = ? AND turf_id = ? AND status = 'confirmed'");
$stmt->bind_param("ssi", $data->date, $data->slot, $data->turf_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Slot just got booked!"]); exit;
}
$stmtK = $conn->prepare("SELECT id FROM blocked_slots WHERE block_date = ? AND slot_time = ? AND turf_id = ?");
$stmtK->bind_param("ssi", $data->date, $data->slot, $data->turf_id);
$stmtK->execute();
if ($stmtK->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Slot is blocked for maintenance."]); exit;
}

// Create user (or add cashback to existing user) - 5% Cashback
$cashback = (int)floor($data->amount * 0.05);
$stmtU = $conn->prepare("INSERT INTO users (name, email, phone, wallet_balance) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email), wallet_balance = wallet_balance + VALUES(wallet_balance)");
$stmtU->bind_param("sssi", $data->name, $data->email, $data->phone, $cashback);
$stmtU->execute();

$stmtF = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$stmtF->bind_param("s", $data->phone);
$stmtF->execute();
$user_id = $stmtF->get_result()->fetch_assoc()['id'];

// Insert booking
$stmtB = $conn->prepare("INSERT INTO bookings (user_id, turf_id, sport, booking_date, slot_time, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
$stmtB->bind_param("iisssi", $user_id, $data->turf_id, $data->sport, $data->date, $data->slot, $data->amount);
$stmtB->execute();
$booking_id = $stmtB->insert_id;

// Save payment record
$stmtP = $conn->prepare("INSERT INTO payments (booking_id, amount, transaction_id) VALUES (?, ?, ?)");
$stmtP->bind_param("iis", $booking_id, $data->amount, $data->txn);
$stmtP->execute();

echo json_encode(["status" => "success", "message" => "Payment Successful! You earned ₹$cashback cashback."]);
?>
