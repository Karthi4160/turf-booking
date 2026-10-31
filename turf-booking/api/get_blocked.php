<?php
include '../db.php';
header('Content-Type: application/json');
$res = $conn->query("SELECT * FROM blocked_slots ORDER BY block_date DESC, id DESC");
$out = []; while ($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>
