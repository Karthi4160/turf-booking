<?php
include '../db.php';
header('Content-Type: application/json');
$res = $conn->query("SELECT * FROM turfs ORDER BY id");
$out = []; while ($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>
