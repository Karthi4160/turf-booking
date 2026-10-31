<?php
include '../db.php';
header('Content-Type: application/json');
$id = (int)$_POST['id'];
$date = $_POST['date'];
$slot = $_POST['slot'];
$turf_id = (int)$_POST['turf_id'];

if ($date < date('Y-m-d')) {
    echo json_encode(["status" => "error", "message" => "Cannot reschedule to a past date."]); exit;
}

// Is the new slot already booked by someone else?
$stmt = $conn->prepare("SELECT id FROM bookings WHERE booking_date=? AND slot_time=? AND turf_id=? AND status='confirmed' AND id<>?");
$stmt->bind_param("ssii", $date, $slot, $turf_id, $id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "New slot is already booked!"]); exit;
}
// Is it blocked by admin?
$stmtK = $conn->prepare("SELECT id FROM blocked_slots WHERE block_date=? AND slot_time=? AND turf_id=?");
$stmtK->bind_param("ssi", $date, $slot, $turf_id);
$stmtK->execute();
if ($stmtK->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "New slot is blocked for maintenance."]); exit;
}

$stmtU = $conn->prepare("UPDATE bookings SET slot_time=?, booking_date=?, status='confirmed' WHERE id=?");
$stmtU->bind_param("ssi", $slot, $date, $id);
$stmtU->execute();
echo json_encode(["status" => "success", "message" => "Booking rescheduled successfully."]);
?>
