<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli("localhost", "root", "", "turf_db");
if ($conn->connect_error) die("DB Failed");
?>
