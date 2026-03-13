<?php
// Database Update Script
// Run this once to update prices and slot names

require_once __DIR__ . '/config/db.php';

// 1. Update hourly_rate to 30.00 for all slots
$conn->query("UPDATE ipark_parking_slots SET hourly_rate = 30.00");
echo "Updated all hourly rates to 30.00.<br>";

// 2. Update slot names to 'Slot 1', 'Slot 2', etc.
$result = $conn->query("SELECT id FROM ipark_parking_slots ORDER BY id");
$counter = 1;

if ($result) {
    $stmt = $conn->prepare("UPDATE ipark_parking_slots SET slot_number = ? WHERE id = ?");
    while ($row = $result->fetch_assoc()) {
        $new_name = 'Slot ' . $counter++;
        $stmt->bind_param("si", $new_name, $row['id']);
        $stmt->execute();
    }
    echo "Renamed $counter slots to 'Slot X' format.<br>";
}
?>