<?php
include '../db.php';
header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

if ($action == 'add_turf') {
    $stmt = $conn->prepare("INSERT INTO turfs (name, location, price_football, price_cricket) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $_POST['name'], $_POST['loc'], $_POST['pf'], $_POST['pc']);
    $stmt->execute();
} elseif ($action == 'update_turf') {
    $stmt = $conn->prepare("UPDATE turfs SET price_football=?, price_cricket=? WHERE id=?");
    $stmt->bind_param("iii", $_POST['pf'], $_POST['pc'], $_POST['id']);
    $stmt->execute();
} elseif ($action == 'delete_turf') {
    $stmt = $conn->prepare("DELETE FROM turfs WHERE id=?");
    $stmt->bind_param("i", $_POST['id']);
    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Cannot delete: this turf has bookings."]); exit;
    }
} elseif ($action == 'block_slot') {
    $stmt = $conn->prepare("INSERT INTO blocked_slots (turf_id, block_date, slot_time) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_POST['turf'], $_POST['date'], $_POST['slot']);
    $stmt->execute();
} elseif ($action == 'unblock_slot') {
    $stmt = $conn->prepare("DELETE FROM blocked_slots WHERE id=?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
}
echo json_encode(["status" => "success"]);
?>
