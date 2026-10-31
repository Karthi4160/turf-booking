<?php
include '../db.php';
header('Content-Type: application/json');
$date = $_GET['date'] ?? '';
$turf_id = (int)($_GET['turf_id'] ?? 0);
$taken = [];

$stmt = $conn->prepare("SELECT slot_time FROM bookings WHERE booking_date = ? AND turf_id = ? AND status = 'confirmed'");
$stmt->bind_param("si", $date, $turf_id); $stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $taken[] = $row['slot_time'];

$stmt2 = $conn->prepare("SELECT slot_time FROM blocked_slots WHERE block_date = ? AND turf_id = ?");
$stmt2->bind_param("si", $date, $turf_id); $stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) $taken[] = $row['slot_time'];

echo json_encode(['booked' => $taken]);
?>
