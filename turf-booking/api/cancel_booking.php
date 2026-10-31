<?php
include '../db.php';
header('Content-Type: application/json');
$id = (int)$_POST['id'];
$stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
echo json_encode(["status" => "success"]);
?>
