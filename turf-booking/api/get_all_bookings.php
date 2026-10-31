<?php
include '../db.php';
header('Content-Type: application/json');
$res = $conn->query("SELECT b.id, u.name, u.phone, t.name AS turf, b.sport, b.booking_date, b.slot_time, b.total_amount, b.status FROM bookings b JOIN users u ON b.user_id=u.id JOIN turfs t ON b.turf_id=t.id ORDER BY b.id DESC");
$out = []; while ($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>
