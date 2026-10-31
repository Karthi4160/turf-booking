<?php
include '../db.php';
header('Content-Type: application/json');
$phone = $_GET['phone'] ?? '';
$stmt = $conn->prepare("SELECT b.*, t.name as turf_name, u.wallet_balance FROM bookings b JOIN users u ON b.user_id=u.id JOIN turfs t ON b.turf_id=t.id WHERE u.phone = ? ORDER BY b.id DESC");
$stmt->bind_param("s", $phone);
$stmt->execute();
$res = $stmt->get_result();
$out = []; while ($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>
